<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class PCDPercentageDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'PCD';
    }

    /**
     * Percentage qualifier (C501/5245), e.g. '1' = allowance,
     * '2' = charge, '3' = discount.
     */
    public function percentageQualifier(): string
    {
        return $this->component(0);
    }

    /**
     * Percentage value (C501/5482): PCD+1:10 -> '10'.
     */
    public function percentage(): string
    {
        return $this->component(1);
    }
}
