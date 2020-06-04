<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use EdifactParser\Exception\MissingSubId;

/** @psalm-immutable */
final class DTMDateTimePeriod implements SegmentInterface
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

    public function subId(): string
    {
        if (!isset($this->rawValues[1][0])) {
            throw new MissingSubId('[1][0]', $this->rawValues);
        }

        return (string) $this->rawValues[1][0];
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }
}
