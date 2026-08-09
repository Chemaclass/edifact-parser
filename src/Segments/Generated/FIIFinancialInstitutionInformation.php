<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * financialInstitutionInformation — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class FIIFinancialInstitutionInformation extends AbstractSegment
{
    public function tag(): string
    {
        return 'FII';
    }

    /**
     * 3035 (an..3)
     */
    public function partyQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C078/3194 (an..35)
     */
    public function accountHolderNumber(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C078/3192 (an..35)
     */
    public function accountHolderName(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C078/3192 (an..35)
     */
    public function accountHolderName2(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C078/6345 (an..3)
     */
    public function currencyCoded(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C088/3433 (an..11)
     */
    public function institutionNameIdentification(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C088/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C088/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C088/3434 (an..17)
     */
    public function institutionBranchNumber(): string
    {
        return $this->component(3, 3);
    }

    /**
     * C088/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(4, 3);
    }

    /**
     * C088/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(5, 3);
    }

    /**
     * C088/3432 (an..70)
     */
    public function institutionName(): string
    {
        return $this->component(6, 3);
    }

    /**
     * C088/3436 (an..70)
     */
    public function institutionBranchPlace(): string
    {
        return $this->component(7, 3);
    }

    /**
     * 3207 (an..3)
     */
    public function countryCoded(): string
    {
        return $this->element(4);
    }
}
