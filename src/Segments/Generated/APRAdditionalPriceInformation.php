<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * additionalPriceInformation — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class APRAdditionalPriceInformation extends AbstractSegment
{
    public function tag(): string
    {
        return 'APR';
    }

    /**
     * 4043 (an..3)
     */
    public function classOfTradeCoded(): string
    {
        return $this->element(1);
    }

    /**
     * C138/5394 (n..12)
     */
    public function priceMultiplier(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C138/5393 (an..3)
     */
    public function priceMultiplierQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C960/4295 (an..3)
     */
    public function changeReasonCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C960/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C960/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C960/4294 (an..35)
     */
    public function changeReason(): string
    {
        return $this->component(3, 3);
    }
}
