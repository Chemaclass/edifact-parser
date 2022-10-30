<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class NullSegment implements SegmentInterface
{
    public function tag(): string
    {
        return '';
    }

    public function subId(): string
    {
        return '';
    }

    public function rawValues(): array
    {
        return [];
    }
}
