<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
interface SegmentInterface
{
    /**
     * A three-character alphanumeric code that identifies the segment.
     */
    public function tag(): string;

    /**
     * The identifier for multiple segments with the same tag.
     */
    public function subId(): string;

    /**
     * Variable length data elements. These can be either simple or composite.
     */
    public function rawValues(): array;
}
