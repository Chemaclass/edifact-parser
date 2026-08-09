<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * allowanceOrCharge — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class ALCAllowanceOrCharge extends AbstractSegment
{
    public function tag(): string
    {
        return 'ALC';
    }

    /**
     * 5463 (an..3)
     */
    public function allowanceOrChargeQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C552/1230 (an..35)
     */
    public function allowanceOrChargeNumber(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C552/5189 (an..3)
     */
    public function chargeallowanceDescriptionCoded(): string
    {
        return $this->component(1, 2);
    }

    /**
     * 4471 (an..3)
     */
    public function settlementCoded(): string
    {
        return $this->element(3);
    }

    /**
     * 1227 (an..3)
     */
    public function calculationSequenceIndicatorCoded(): string
    {
        return $this->element(4);
    }

    /**
     * C214/7161 (an..3)
     */
    public function specialServicesCoded(): string
    {
        return $this->firstComponent(5);
    }

    /**
     * C214/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 5);
    }

    /**
     * C214/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 5);
    }

    /**
     * C214/7160 (an..35)
     */
    public function specialService(): string
    {
        return $this->component(3, 5);
    }

    /**
     * C214/7160 (an..35)
     */
    public function specialService2(): string
    {
        return $this->component(4, 5);
    }
}
