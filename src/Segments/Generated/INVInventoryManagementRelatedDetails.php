<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * inventoryManagementRelatedDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class INVInventoryManagementRelatedDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'INV';
    }

    /**
     * 4501 (an..3)
     */
    public function inventoryMovementDirectionCoded(): string
    {
        return $this->element(1);
    }

    /**
     * 7491 (an..3)
     */
    public function typeOfInventoryAffectedCoded(): string
    {
        return $this->element(2);
    }

    /**
     * 4499 (an..3)
     */
    public function reasonForInventoryMovementCoded(): string
    {
        return $this->element(3);
    }

    /**
     * 4503 (an..3)
     */
    public function inventoryBalanceMethodCoded(): string
    {
        return $this->element(4);
    }

    /**
     * C522/4403 (an..3)
     */
    public function instructionQualifier(): string
    {
        return $this->firstComponent(5);
    }

    /**
     * C522/4401 (an..3)
     */
    public function instructionCoded(): string
    {
        return $this->component(1, 5);
    }

    /**
     * C522/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(2, 5);
    }

    /**
     * C522/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(3, 5);
    }

    /**
     * C522/4400 (an..35)
     */
    public function instruction(): string
    {
        return $this->component(4, 5);
    }
}
