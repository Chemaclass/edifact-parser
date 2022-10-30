<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
class RFFReference implements SegmentInterface
{
    public function __construct(private array $rawValues)
    {
    }

    public function tag(): string
    {
        return 'RFF';
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
