<?php

declare(strict_types=1);

namespace EdifactParser\Writer;

use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;

use function count;

/**
 * Assembles a single UNH...UNT message: prepend the header, append the trailer, and
 * compute the UNT segment count automatically.
 */
final class MessageBuilder
{
    /** @var list<SegmentInterface> */
    private array $segments = [];

    private function __construct(
        private string $reference,
        private string $type,
        private string $version,
        private string $release,
        private string $agency,
    ) {
    }

    public static function create(
        string $reference,
        string $type,
        string $version = 'D',
        string $release = '96A',
        string $agency = 'UN',
    ): self {
        return new self($reference, $type, $version, $release, $agency);
    }

    public function addSegment(SegmentInterface $segment): self
    {
        $this->segments[] = $segment;

        return $this;
    }

    public function reference(): string
    {
        return $this->reference;
    }

    /**
     * @return list<SegmentInterface> [UNH, ...body, UNT] with the UNT segment count filled in
     */
    public function build(): array
    {
        $header = new UNHMessageHeader(['UNH', $this->reference, [$this->type, $this->version, $this->release, $this->agency]]);
        $segmentCount = count($this->segments) + 2; // body + UNH + UNT
        $trailer = new UNTMessageFooter(['UNT', (string) $segmentCount, $this->reference]);

        return [$header, ...$this->segments, $trailer];
    }
}
