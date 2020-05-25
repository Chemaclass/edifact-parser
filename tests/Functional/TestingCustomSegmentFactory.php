<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Functional;

use EdifactParser\Segments\CustomSegmentFactoryInterface;
use EdifactParser\Segments\SegmentInterface;

final class TestingCustomSegmentFactory implements CustomSegmentFactoryInterface
{
    private string $customKey;

    public function __construct(string $customKey)
    {
        $this->customKey = $customKey;
    }

    public function segmentFromArray(array $rawArray): ?SegmentInterface
    {
        if ($this->customKey !== $rawArray[0]) {
            return null;
        }

        return new class($rawArray) implements SegmentInterface {
            private array $rawArray;

            public function __construct(array $rawArray)
            {
                $this->rawArray = $rawArray;
            }

            public function name(): string
            {
                return $this->rawArray[0];
            }

            public function subSegmentKey(): string
            {
                return $this->rawArray[1];
            }

            public function rawValues(): array
            {
                return $this->rawArray;
            }
        };
    }
}
