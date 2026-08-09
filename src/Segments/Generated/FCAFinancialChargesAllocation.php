<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * financialChargesAllocation — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class FCAFinancialChargesAllocation extends AbstractSegment
{
    public function tag(): string
    {
        return 'FCA';
    }

    /**
     * 4471 (an..3)
     */
    public function settlementCoded(): string
    {
        return $this->element(1);
    }

    /**
     * C878/3434 (an..17)
     */
    public function institutionBranchNumber(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C878/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C878/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C878/3194 (an..35)
     */
    public function accountHolderNumber(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C878/6345 (an..3)
     */
    public function currencyCoded(): string
    {
        return $this->component(4, 2);
    }
}
