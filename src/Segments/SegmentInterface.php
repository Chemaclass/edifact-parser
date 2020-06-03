<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
interface SegmentInterface
{
    public function tag(): string;

    public function subSegmentKey(): string;

    public function rawValues(): array;
}
