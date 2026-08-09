<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * chargePaymentInstructions — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CPIChargePaymentInstructions extends AbstractSegment
{
    public function tag(): string
    {
        return 'CPI';
    }

    /**
     * C229/5237 (an..3)
     */
    public function chargeCategoryCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C229/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C229/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C231/4215 (an..3)
     */
    public function transportChargesMethodOfPaymentCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C231/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C231/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 2);
    }

    /**
     * 4237 (an..3)
     */
    public function prepaidcollectIndicatorCoded(): string
    {
        return $this->element(3);
    }
}
