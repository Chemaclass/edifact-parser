<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * valueListIdentification — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class VLIValueListIdentification extends AbstractSegment
{
    public function tag(): string
    {
        return 'VLI';
    }

    /**
     * C780/1518 (an..35)
     */
    public function valueListIdentifier(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C780/7405 (an..3)
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
     * 1514 (an..70)
     */
    public function valueListName(): string
    {
        return $this->element(4);
    }

    /**
     * 1507 (an..3)
     */
    public function classDesignatorCoded(): string
    {
        return $this->element(5);
    }

    /**
     * 1505 (an..3)
     */
    public function valueListTypeCoded(): string
    {
        return $this->element(6);
    }

    /**
     * C240/7037 (an..17)
     */
    public function characteristicIdentification(): string
    {
        return $this->firstComponent(7);
    }

    /**
     * C240/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 7);
    }

    /**
     * C240/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 7);
    }

    /**
     * C240/7036 (an..35)
     */
    public function characteristic(): string
    {
        return $this->component(3, 7);
    }

    /**
     * C240/7036 (an..35)
     */
    public function characteristic2(): string
    {
        return $this->component(4, 7);
    }

    /**
     * 4513 (an..3)
     */
    public function maintenanceOperationCoded(): string
    {
        return $this->element(8);
    }
}
