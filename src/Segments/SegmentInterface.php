<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

interface SegmentInterface
{
    public function name(): string;

    public function subSegmentKey(): string;

    public function rawValues();
}
