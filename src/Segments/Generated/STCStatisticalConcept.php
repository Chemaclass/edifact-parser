<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * statisticalConcept — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class STCStatisticalConcept extends AbstractSegment
{
    public function tag(): string
    {
        return 'STC';
    }

    /**
     * C785/6434 (an..35)
     */
    public function statisticalConceptIdentifier(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C785/7405 (an..3)
     */
    public function identityNumberQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C082/3039 (an..35)
     */
    public function partyIdIdentification(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C082/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C082/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * 4405 (an..3)
     */
    public function statusCoded(): string
    {
        return $this->element(3);
    }

    /**
     * 4513 (an..3)
     */
    public function maintenanceOperationCoded(): string
    {
        return $this->element(4);
    }
}
