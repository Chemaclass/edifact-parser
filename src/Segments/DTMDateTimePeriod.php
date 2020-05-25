<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

final class DTMDateTimePeriod implements SegmentInterface
{
    public const NAME = 'DTM';

    private array $rawValues;

    public function __construct(array $rawValues)
    {
        $this->rawValues = $rawValues;
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function subSegmentKey(): string
    {
        return $this->rawValues[1][0];
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }
}
