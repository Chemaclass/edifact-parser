<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class BGMBeginningOfMessage implements SegmentInterface
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
        return (string) $this->rawValues[1];
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }
}
