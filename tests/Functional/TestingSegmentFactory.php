<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Functional;

use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\SegmentFactoryInterface;
use EdifactParser\Segments\SegmentInterface;

/** @psalm-immutable */
final class TestingSegmentFactory implements SegmentFactoryInterface
{
    private string $customKey;

    private SegmentFactory $defaultFactory;

    public function __construct(string $customKey)
    {
        $this->customKey = $customKey;
        $this->defaultFactory = new SegmentFactory();
    }

    public function segmentFromArray(array $rawArray): SegmentInterface
    {
        if ($this->customKey !== $rawArray[0]) {
            return $this->defaultFactory->segmentFromArray($rawArray);
        }

        return new /** @psalm-immutable */ class($rawArray) implements SegmentInterface {
            private array $rawArray;

            public function __construct(array $rawArray)
            {
                $this->rawArray = $rawArray;
            }

            public function name(): string
            {
                return (string) $this->rawArray[0];
            }

            public function subSegmentKey(): string
            {
                return (string) $this->rawArray[1];
            }

            public function rawValues(): array
            {
                return $this->rawArray;
            }
        };
    }
}
