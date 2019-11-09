<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\CustomSegmentFactoryInterface;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\SegmentInterface;

final class SegmentedValues
{
    /** @var array */
    private $list;

    public static function fromRaw(
        array $rawArrays,
        ?CustomSegmentFactoryInterface $customSegmentsFactory = null
    ): self {
        $factory = new SegmentFactory($customSegmentsFactory);

        $self = new self();
        $segments = [];

        foreach ($rawArrays as $rawArray) {
            $segments[] = $factory->segmentFromArray($rawArray);
        }

        foreach ($segments as $segment) {
            $self->addSegment($segment);
        }

        return $self;
    }

    private function addSegment(SegmentInterface $segment): void
    {
        $this->list[] = $segment;
    }

    /** @return SegmentInterface[] */
    public function list(): array
    {
        return $this->list;
    }
}
