<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
interface SegmentFactoryInterface
{
    public function createSegmentFromArray(array $rawArray): SegmentInterface;
}
