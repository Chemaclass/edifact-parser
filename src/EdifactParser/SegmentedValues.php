<?php

declare(strict_types=1);

namespace App\EdifactParser;

use App\EdifactParser\Segments\SegmentFactory;
use App\EdifactParser\Segments\SegmentInterface;

final class SegmentedValues
{
    /** @var array */
    private $list;

    public static function fromRaw(array $rawArrays): self
    {
        $segments = [];

        foreach ($rawArrays as $rawArray) {
            $segments[] = SegmentFactory::factory($rawArray);
        }

        return self::fromSegmentInterfaces($segments);
    }

    private static function fromSegmentInterfaces(array $segments): self
    {
        $self = new self();

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
