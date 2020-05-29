<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

interface SegmentFactoryInterface
{
    public function segmentFromArray(array $rawArray): SegmentInterface;
}
