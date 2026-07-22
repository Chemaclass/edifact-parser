<?php

declare(strict_types=1);

namespace EdifactParser\Writer;

use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNBInterchangeHeader;
use EdifactParser\Segments\UNZInterchangeTrailer;
use EdifactParser\Serializer\EdifactSerializer;
use EdifactParser\Serializer\UnaSeparators;

use function count;

/**
 * Assembles a complete interchange: UNB header, the messages, and a UNZ trailer with
 * the interchange control count filled in automatically. Serialize the result to a
 * ready-to-send EDIFACT string with {@see self::toString()}.
 */
final class InterchangeBuilder
{
    /** @var list<MessageBuilder> */
    private array $messages = [];

    private string $date = '';

    private string $time = '';

    private function __construct(
        private string $sender,
        private string $recipient,
        private string $controlReference,
        private string $syntaxIdentifier,
        private string $syntaxVersion,
    ) {
    }

    public static function create(
        string $sender,
        string $recipient,
        string $controlReference,
        string $syntaxIdentifier = 'UNOC',
        string $syntaxVersion = '3',
    ): self {
        return new self($sender, $recipient, $controlReference, $syntaxIdentifier, $syntaxVersion);
    }

    public function preparedAt(string $date, string $time): self
    {
        $this->date = $date;
        $this->time = $time;

        return $this;
    }

    public function addMessage(MessageBuilder $message): self
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @return list<SegmentInterface> [UNB, ...messages, UNZ] with the interchange control count filled in
     */
    public function build(): array
    {
        $header = new UNBInterchangeHeader([
            'UNB',
            [$this->syntaxIdentifier, $this->syntaxVersion],
            [$this->sender],
            [$this->recipient],
            [$this->date, $this->time],
            $this->controlReference,
        ]);

        $segments = [$header];
        foreach ($this->messages as $message) {
            foreach ($message->build() as $segment) {
                $segments[] = $segment;
            }
        }
        $segments[] = new UNZInterchangeTrailer(['UNZ', (string) count($this->messages), $this->controlReference]);

        return $segments;
    }

    public function toString(?UnaSeparators $una = null): string
    {
        return (new EdifactSerializer($una))->serialize($this->build(), includeUna: true);
    }
}
