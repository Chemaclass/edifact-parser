<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * paymentInstructions — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class PAIPaymentInstructions extends AbstractSegment
{
    public function tag(): string
    {
        return 'PAI';
    }

    /**
     * C534/4439 (an..3)
     */
    public function paymentConditionsCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C534/4431 (an..3)
     */
    public function paymentGuaranteeCoded(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C534/4461 (an..3)
     */
    public function paymentMeansCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C534/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C534/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(4, 1);
    }

    /**
     * C534/4435 (an..3)
     */
    public function paymentChannelCoded(): string
    {
        return $this->component(5, 1);
    }
}
