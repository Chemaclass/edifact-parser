<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * transportServiceRequirements — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class TSRTransportServiceRequirements extends AbstractSegment
{
    public function tag(): string
    {
        return 'TSR';
    }

    /**
     * C536/4065 (an..3)
     */
    public function contractAndCarriageConditionCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C536/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C536/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C233/7273 (an..3)
     */
    public function serviceRequirementCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C233/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C233/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C233/7273 (an..3)
     */
    public function serviceRequirementCoded2(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C233/1131 (an..3)
     */
    public function codeListQualifier3(): string
    {
        return $this->component(4, 2);
    }

    /**
     * C233/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded3(): string
    {
        return $this->component(5, 2);
    }

    /**
     * C537/4219 (an..3)
     */
    public function transportPriorityCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C537/1131 (an..3)
     */
    public function codeListQualifier4(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C537/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded4(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C703/7085 (an..3)
     */
    public function natureOfCargoCoded(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C703/1131 (an..3)
     */
    public function codeListQualifier5(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C703/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded5(): string
    {
        return $this->component(2, 4);
    }
}
