<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

/**
 * Fake Segment which does not implements the SegmentInterface.
 *
 * @psalm-immutable
 */
final class NONFakeSegment
{
    public array $rawValues;

    /** @psalm-pure */
    public static function createFromArray(array $rawValues): self
    {
        return new self($rawValues);
    }

    private function __construct(array $rawValues)
    {
        $this->rawValues = $rawValues;
    }
}
