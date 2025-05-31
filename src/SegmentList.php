<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\SegmentFactoryInterface;
use EdifactParser\Segments\SegmentInterface;

final class SegmentList
{
    public function __construct(private SegmentFactoryInterface $segmentFactory)
    {
    }

    public static function withDefaultFactory(): self
    {
        return new self(SegmentFactory::withDefaultSegments());
    }

    /**
     * @param  array<mixed>  $rawArrays
     *
     * @return list<SegmentInterface>
     */
    public function fromRaw(array $rawArrays): array
    {
        $segments = [];
        foreach ($rawArrays as $raw) {
            $segments[] = $this->segmentFactory->createSegmentFromArray($raw);
        }

        return $segments;
    }
}
