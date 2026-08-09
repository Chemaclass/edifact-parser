<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * equipmentDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class EQDEquipmentDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'EQD';
    }

    /**
     * 8053 (an..3)
     */
    public function equipmentQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C237/8260 (an..17)
     */
    public function equipmentIdentificationNumber(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C237/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C237/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C237/3207 (an..3)
     */
    public function countryCoded(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C224/8155 (an..10)
     */
    public function equipmentSizeAndTypeIdentification(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C224/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C224/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C224/8154 (an..35)
     */
    public function equipmentSizeAndType(): string
    {
        return $this->component(3, 3);
    }

    /**
     * 8077 (an..3)
     */
    public function equipmentSupplierCoded(): string
    {
        return $this->element(4);
    }

    /**
     * 8249 (an..3)
     */
    public function equipmentStatusCoded(): string
    {
        return $this->element(5);
    }

    /**
     * 8169 (an..3)
     */
    public function fullemptyIndicatorCoded(): string
    {
        return $this->element(6);
    }
}
