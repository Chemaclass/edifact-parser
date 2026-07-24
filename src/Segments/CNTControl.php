<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class CNTControl extends AbstractSegment
{
    public function tag(): string
    {
        return 'CNT';
    }

    public function subId(): string
    {
        return $this->requiredSubId();
    }

    /**
     * Control total type code qualifier (C270/6069), e.g. '2' = number of line
     * items, '7' = gross weight.
     */
    public function controlQualifier(): string
    {
        return $this->component(0);
    }

    /**
     * Control value (C270/6066): CNT+7:0.62:KGM -> '0.62'.
     */
    public function controlValue(): string
    {
        return $this->component(1);
    }

    /**
     * Measure unit code when present (C270/6411): CNT+7:0.62:KGM -> 'KGM'.
     */
    public function measureUnit(): string
    {
        return $this->component(2);
    }
}
