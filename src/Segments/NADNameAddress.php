<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class NADNameAddress implements SegmentInterface
{
    private array $rawValues;

    public function __construct(array $rawValues)
    {
        $this->rawValues = $rawValues;
    }

    public function tag(): string
    {
        return 'NAD';
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
