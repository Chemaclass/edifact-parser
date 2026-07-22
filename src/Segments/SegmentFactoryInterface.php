<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

interface SegmentFactoryInterface
{
    public function createSegmentFromArray(array $rawArray): SegmentInterface;
}
