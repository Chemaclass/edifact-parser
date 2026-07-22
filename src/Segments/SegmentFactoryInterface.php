<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

interface SegmentFactoryInterface
{
    /**
     * @param array<int, string|array<int, string>> $rawArray
     */
    public function createSegmentFromArray(array $rawArray): SegmentInterface;
}
