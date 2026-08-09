<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * businessFunction — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class BUSBusinessFunction extends AbstractSegment
{
    public function tag(): string
    {
        return 'BUS';
    }

    /**
     * C521/4027 (an..3)
     */
    public function businessFunctionQualifier(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C521/4025 (an..3)
     */
    public function businessFunctionCoded(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C521/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C521/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C521/4022 (an..70)
     */
    public function businessDescription(): string
    {
        return $this->component(4, 1);
    }

    /**
     * 3279 (an..3)
     */
    public function geographicEnvironmentCoded(): string
    {
        return $this->element(2);
    }

    /**
     * 4487 (an..3)
     */
    public function typeOfFinancialTransactionCoded(): string
    {
        return $this->element(3);
    }

    /**
     * C551/4383 (an..3)
     */
    public function bankOperationCoded(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C551/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C551/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 4);
    }

    /**
     * 4463 (an..3)
     */
    public function intracompanyPaymentCoded(): string
    {
        return $this->element(5);
    }
}
