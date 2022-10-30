<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class QTYQuantity implements SegmentInterface
{
    private array $rawValues;

    public function __construct(array $rawValues)
    {
        $this->rawValues = $rawValues;
    }

    public function tag(): string
    {
        return 'QTY';
    }

    public function subId(): string
    {
        return $this->rawValues[1][0];
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }
}
