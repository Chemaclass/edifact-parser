<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
class UNSSectionControl implements SegmentInterface
{
    private array $rawValues;

    public function __construct(array $rawValues)
    {
        $this->rawValues = $rawValues;
    }

    public function tag(): string
    {
        return 'UNS';
    }

    public function subId(): string
    {
        return $this->rawValues[1];
    }

    public function rawValues(): array
    {
        return $this->rawValues;
    }

    public function getIdentifier(): SectionControlIdentifier
    {
        return SectionControlIdentifier::from($this->rawValues[1]);
    }
}
