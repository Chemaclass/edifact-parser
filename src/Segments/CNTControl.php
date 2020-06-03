<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

namespace EdifactParser\Segments;

use EdifactParser\Exception\MissingSubSegmentKey;

/** @psalm-immutable */
final class CNTControl implements SegmentInterface
{
    private array $rawValues;

    public function __construct(array $rawValues)
    {
        $this->rawValues = $rawValues;
    }

    public function tag(): string
    {
        return self::class;
    }

    public function subSegmentKey(): string
    {
        if (!isset($this->rawValues[1][0])) {
            throw new MissingSubSegmentKey('[1][0]', $this->rawValues);
        }

        return (string) $this->rawValues[1][0];
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }
}
