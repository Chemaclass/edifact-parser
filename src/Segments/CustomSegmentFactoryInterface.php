<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

interface CustomSegmentFactoryInterface
{
    public function segmentFromArray(array $rawArray): ?SegmentInterface;
}
