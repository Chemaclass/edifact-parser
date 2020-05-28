<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

final class UnknownSegment implements SegmentInterface
{
    private array $rawValues;

    public function __construct(array $rawValues)
    {
        $this->rawValues = $rawValues;
    }

    public function name(): string
    {
        return self::class;
    }

    public function subSegmentKey(): string
    {
        $encodedValues = json_encode($this->rawValues);

        return ($encodedValues) ? md5($encodedValues) : md5(self::class);
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }
}
