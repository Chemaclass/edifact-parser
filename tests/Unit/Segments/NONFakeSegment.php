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
    private function __construct(public array $rawValues)
    {
    }
}
