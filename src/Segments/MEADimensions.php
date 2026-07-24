<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class MEADimensions extends AbstractSegment
{
    public function tag(): string
    {
        return 'MEA';
    }

    /**
     * Measurement purpose qualifier (6311), e.g. 'WT' = weight, 'VOL' = volume.
     */
    public function measurementPurpose(): string
    {
        return $this->element(1);
    }

    /**
     * Measured attribute code (C502/6313), e.g. 'G' = gross. Empty when omitted:
     * MEA+VOL++MTQ:0 -> ''.
     */
    public function measuredAttribute(): string
    {
        return $this->element(2);
    }

    /**
     * Measurement unit code (C174/6411): MEA+WT+G+KGM:0.62 -> 'KGM'.
     */
    public function unitCode(): string
    {
        return $this->component(0, 3);
    }

    /**
     * Measurement value (C174/6314): MEA+WT+G+KGM:0.62 -> '0.62'.
     */
    public function value(): string
    {
        return $this->component(1, 3);
    }
}
