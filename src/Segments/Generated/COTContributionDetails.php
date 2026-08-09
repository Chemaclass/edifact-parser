<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * contributionDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class COTContributionDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'COT';
    }

    /**
     * 5047 (an..3)
     */
    public function contributionQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C953/5049 (an..3)
     */
    public function contributionTypeCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C953/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C953/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C953/5048 (an..35)
     */
    public function contributionType(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C522/4403 (an..3)
     */
    public function instructionQualifier(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C522/4401 (an..3)
     */
    public function instructionCoded(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C522/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C522/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(3, 3);
    }

    /**
     * C522/4400 (an..35)
     */
    public function instruction(): string
    {
        return $this->component(4, 3);
    }

    /**
     * C203/5243 (an..9)
     */
    public function ratetariffClassIdentification(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C203/1131 (an..3)
     */
    public function codeListQualifier3(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C203/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded3(): string
    {
        return $this->component(2, 4);
    }

    /**
     * C203/5242 (an..35)
     */
    public function ratetariffClass(): string
    {
        return $this->component(3, 4);
    }

    /**
     * C203/5275 (an..6)
     */
    public function supplementaryRatetariffBasisIdentification(): string
    {
        return $this->component(4, 4);
    }

    /**
     * C203/1131 (an..3)
     */
    public function codeListQualifier4(): string
    {
        return $this->component(5, 4);
    }

    /**
     * C203/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded4(): string
    {
        return $this->component(6, 4);
    }

    /**
     * C203/5275 (an..6)
     */
    public function supplementaryRatetariffBasisIdentification2(): string
    {
        return $this->component(7, 4);
    }

    /**
     * C203/1131 (an..3)
     */
    public function codeListQualifier5(): string
    {
        return $this->component(8, 4);
    }

    /**
     * C203/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded5(): string
    {
        return $this->component(9, 4);
    }

    /**
     * C960/4295 (an..3)
     */
    public function changeReasonCoded(): string
    {
        return $this->firstComponent(5);
    }

    /**
     * C960/1131 (an..3)
     */
    public function codeListQualifier6(): string
    {
        return $this->component(1, 5);
    }

    /**
     * C960/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded6(): string
    {
        return $this->component(2, 5);
    }

    /**
     * C960/4294 (an..35)
     */
    public function changeReason(): string
    {
        return $this->component(3, 5);
    }
}
