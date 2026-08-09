<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * transportChargerateCalculations — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class TCCTransportChargerateCalculations extends AbstractSegment
{
    public function tag(): string
    {
        return 'TCC';
    }

    /**
     * C200/8023 (an..17)
     */
    public function freightAndChargesIdentification(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C200/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C200/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C200/8022 (an..26)
     */
    public function freightAndCharges(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C200/4237 (an..3)
     */
    public function prepaidcollectIndicatorCoded(): string
    {
        return $this->component(4, 1);
    }

    /**
     * C200/7140 (an..35)
     */
    public function itemNumber(): string
    {
        return $this->component(5, 1);
    }

    /**
     * C203/5243 (an..9)
     */
    public function ratetariffClassIdentification(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C203/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C203/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C203/5242 (an..35)
     */
    public function ratetariffClass(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C203/5275 (an..6)
     */
    public function supplementaryRatetariffBasisIdentification(): string
    {
        return $this->component(4, 2);
    }

    /**
     * C203/1131 (an..3)
     */
    public function codeListQualifier3(): string
    {
        return $this->component(5, 2);
    }

    /**
     * C203/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded3(): string
    {
        return $this->component(6, 2);
    }

    /**
     * C203/5275 (an..6)
     */
    public function supplementaryRatetariffBasisIdentification2(): string
    {
        return $this->component(7, 2);
    }

    /**
     * C203/1131 (an..3)
     */
    public function codeListQualifier4(): string
    {
        return $this->component(8, 2);
    }

    /**
     * C203/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded4(): string
    {
        return $this->component(9, 2);
    }

    /**
     * C528/7357 (an..18)
     */
    public function commodityrateIdentification(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C528/1131 (an..3)
     */
    public function codeListQualifier5(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C528/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded5(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C554/5243 (an..9)
     */
    public function ratetariffClassIdentification2(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C554/1131 (an..3)
     */
    public function codeListQualifier6(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C554/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded6(): string
    {
        return $this->component(2, 4);
    }
}
