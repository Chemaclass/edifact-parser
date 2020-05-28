<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\UNHMessageHeader;

/**
 * A transactionResult is a list of transactionMessages.
 *
 * @psalmphp-immutable
 */
final class TransactionResult
{
    /** @psalm-var list<TransactionMessage> */
    private array $messages;

    public static function fromSegmentedValues(SegmentedValues $values): self
    {
        return new self($values);
    }

    private function __construct(SegmentedValues $values)
    {
        /** @var TransactionMessage[] $messages */
        $messages = [];
        /** @var ?TransactionMessage $message */
        $message = null;

        foreach ($values->list() as $segment) {
            if ($segment instanceof UNHMessageHeader) {
                if ($message) {
                    $messages[] = $message;
                }
                $message = new TransactionMessage();
            }

            if ($message) {
                $message->addSegment($segment);
            }
        }

        if ($message) {
            $messages[] = $message;
        }

        $this->messages = $messages;
    }

    /** @psalm-return list<TransactionMessage> */
    public function messages(): array
    {
        return $this->messages;
    }
}
