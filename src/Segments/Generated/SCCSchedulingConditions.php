<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * schedulingConditions — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class SCCSchedulingConditions extends AbstractSegment
{
    public function tag(): string
    {
        return 'SCC';
    }

    /**
     * 4017 (an..3)
     */
    public function deliveryPlanStatusIndicatorCoded(): string
    {
        return $this->element(1);
    }

    /**
     * 4493 (an..3)
     */
    public function deliveryRequirementsCoded(): string
    {
        return $this->element(2);
    }

    /**
     * C329/2013 (an..3)
     */
    public function frequencyCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C329/2015 (an..3)
     */
    public function despatchPatternCoded(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C329/2017 (an..3)
     */
    public function despatchPatternTimingCoded(): string
    {
        return $this->component(2, 3);
    }
}
