<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * membershipDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class MEMMembershipDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'MEM';
    }

    /**
     * 7449 (an..3)
     */
    public function membershipQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C942/7451 (an..4)
     */
    public function membershipCategoryIdentification(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C942/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C942/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C942/7450 (an..35)
     */
    public function membershipCategory(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C944/7453 (an..3)
     */
    public function membershipStatusCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C944/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C944/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C944/7452 (an..35)
     */
    public function membershipStatus(): string
    {
        return $this->component(3, 3);
    }

    /**
     * C945/7455 (an..3)
     */
    public function membershipLevelQualifier(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C945/7457 (an..9)
     */
    public function membershipLevelIdentification(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C945/1131 (an..3)
     */
    public function codeListQualifier3(): string
    {
        return $this->component(2, 4);
    }

    /**
     * C945/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded3(): string
    {
        return $this->component(3, 4);
    }

    /**
     * C945/7456 (an..35)
     */
    public function membershipLevel(): string
    {
        return $this->component(4, 4);
    }

    /**
     * C203/5243 (an..9)
     */
    public function ratetariffClassIdentification(): string
    {
        return $this->firstComponent(5);
    }

    /**
     * C203/1131 (an..3)
     */
    public function codeListQualifier4(): string
    {
        return $this->component(1, 5);
    }

    /**
     * C203/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded4(): string
    {
        return $this->component(2, 5);
    }

    /**
     * C203/5242 (an..35)
     */
    public function ratetariffClass(): string
    {
        return $this->component(3, 5);
    }

    /**
     * C203/5275 (an..6)
     */
    public function supplementaryRatetariffBasisIdentification(): string
    {
        return $this->component(4, 5);
    }

    /**
     * C203/1131 (an..3)
     */
    public function codeListQualifier5(): string
    {
        return $this->component(5, 5);
    }

    /**
     * C203/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded5(): string
    {
        return $this->component(6, 5);
    }

    /**
     * C203/5275 (an..6)
     */
    public function supplementaryRatetariffBasisIdentification2(): string
    {
        return $this->component(7, 5);
    }

    /**
     * C203/1131 (an..3)
     */
    public function codeListQualifier6(): string
    {
        return $this->component(8, 5);
    }

    /**
     * C203/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded6(): string
    {
        return $this->component(9, 5);
    }

    /**
     * C960/4295 (an..3)
     */
    public function changeReasonCoded(): string
    {
        return $this->firstComponent(6);
    }

    /**
     * C960/1131 (an..3)
     */
    public function codeListQualifier7(): string
    {
        return $this->component(1, 6);
    }

    /**
     * C960/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded7(): string
    {
        return $this->component(2, 6);
    }

    /**
     * C960/4294 (an..35)
     */
    public function changeReason(): string
    {
        return $this->component(3, 6);
    }
}
