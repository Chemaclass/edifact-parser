<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\AbstractSegment;

/**
 * A stand-in for a user-registered segment. It deliberately mixes a plain accessor with a
 * method that takes an argument and one with an optional argument, which is what
 * {@see \EdifactParser\Segments\SegmentDescriptor} has to tell apart.
 *
 * @psalm-immutable
 */
final class EQDFakeSegment extends AbstractSegment
{
    public function tag(): string
    {
        return 'EQD';
    }

    public function equipmentTypeCode(): string
    {
        return $this->component(0);
    }

    /**
     * Takes a required argument, so it describes behaviour rather than a field.
     */
    public function componentAt(int $index): string
    {
        return $this->component($index);
    }

    /**
     * Optional argument only, so it still reads as a field.
     */
    public function sizeTypeCode(int $group = 2): string
    {
        return $this->component(0, $group);
    }
}
