<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\SegmentFactoryInterface;
use EdifactParser\Segments\SegmentInterface;

/** @psalm-immutable */
final class SegmentedValues
{
    private SegmentFactoryInterface $factory;

    /** @psalm-pure */
    public static function factory(?SegmentFactoryInterface $segmentFactory = null): self
    {
        return new self($segmentFactory ?? new SegmentFactory());
    }

    private function __construct(SegmentFactoryInterface $segmentFactory)
    {
        $this->factory = $segmentFactory;
    }

    /** @return SegmentInterface[] */
    public function fromRaw(array $rawArrays): array
    {
        return array_map(
            fn (array $raw) => $this->factory->segmentFromArray($raw),
            $rawArrays
        );
    }
}
