<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class LOCPlace extends AbstractSegment
{
    public function tag(): string
    {
        return 'LOC';
    }

    /**
     * Place/location qualifier (e.g., '5' = Place of departure, '8' = Place of destination)
     */
    public function locationQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * Location identification (e.g., a UN/LOCODE)
     */
    public function locationId(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * Location name (free-form), when present in the composite
     */
    public function locationName(): string
    {
        return $this->component(3, 2);
    }
}
