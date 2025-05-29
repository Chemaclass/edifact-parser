<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Functional;

use EdifactParser\Segments\AbstractSegment;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\SegmentFactoryInterface;
use EdifactParser\Segments\SegmentInterface;

/** @psalm-immutable */
final class TestingSegmentFactory implements SegmentFactoryInterface
{
    private SegmentFactory $defaultFactory;

    public function __construct(private string $customKey)
    {
        $this->defaultFactory = SegmentFactory::withDefaultSegments();
    }

    public function createSegmentFromArray(array $rawArray): SegmentInterface
    {
        if ($this->customKey !== $rawArray[0]) {
            return $this->defaultFactory->createSegmentFromArray($rawArray);
        }

        return new /** @psalm-immutable */ class($rawArray) extends AbstractSegment {
            public function tag(): string
            {
                return (string) $this->rawValues[0];
            }
        };
    }
}
