<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class DTMDateTimePeriod implements SegmentInterface
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
        if (!isset($this->rawValues[1][0])) {
            throw new \Exception('missing sub segment key');
        }

        return (string) $this->rawValues[1][0];
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }
}
