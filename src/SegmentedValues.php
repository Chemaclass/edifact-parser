<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\SegmentFactoryInterface;
use EdifactParser\Segments\SegmentInterface;

final class SegmentedValues
{
    /** @psalm-var list<SegmentInterface> */
    private array $list = [];

    private SegmentFactoryInterface $segmentFactory;

    public static function factory(?SegmentFactoryInterface $segmentFactory = null): self
    {
        return new self($segmentFactory ?? new SegmentFactory());
    }

    public function __construct(SegmentFactoryInterface $segmentFactory)
    {
        $this->segmentFactory = $segmentFactory;
    }

    public function fromRaw(array $rawArrays): self
    {
        foreach ($rawArrays as $rawArray) {
            $this->list[] = $this->segmentFactory->segmentFromArray($rawArray);
        }

        return $this;
    }

    /** @return SegmentInterface[] */
    public function list(): array
    {
        return $this->list;
    }
}
