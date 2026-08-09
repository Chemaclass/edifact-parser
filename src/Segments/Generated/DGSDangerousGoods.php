<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * dangerousGoods — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class DGSDangerousGoods extends AbstractSegment
{
    public function tag(): string
    {
        return 'DGS';
    }

    /**
     * 8273 (an..3)
     */
    public function dangerousGoodsRegulationsCoded(): string
    {
        return $this->element(1);
    }

    /**
     * C205/8351 (an..7)
     */
    public function hazardCodeIdentification(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C205/8078 (an..7)
     */
    public function hazardSubstanceitempageNumber(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C205/8092 (an..10)
     */
    public function hazardCodeVersionNumber(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C234/7124 (n)
     */
    public function undgNumber(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C234/7088 (an..8)
     */
    public function dangerousGoodsFlashpoint(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C223/7106 (n)
     */
    public function shipmentFlashpoint(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C223/6411 (an..3)
     */
    public function measureUnitQualifier(): string
    {
        return $this->component(1, 4);
    }

    /**
     * 8339 (an..3)
     */
    public function packingGroupCoded(): string
    {
        return $this->element(5);
    }

    /**
     * 8364 (an..6)
     */
    public function emsNumber(): string
    {
        return $this->element(6);
    }

    /**
     * 8410 (an..4)
     */
    public function mfag(): string
    {
        return $this->element(7);
    }

    /**
     * 8126 (an..10)
     */
    public function tremCardNumber(): string
    {
        return $this->element(8);
    }

    /**
     * C235/8158 (an..4)
     */
    public function hazardIdentificationNumberUpperPart(): string
    {
        return $this->firstComponent(9);
    }

    /**
     * C235/8186 (an)
     */
    public function substanceIdentificationNumberLowerPart(): string
    {
        return $this->component(1, 9);
    }

    /**
     * C236/8246 (an..4)
     */
    public function dangerousGoodsLabelMarking(): string
    {
        return $this->firstComponent(10);
    }

    /**
     * C236/8246 (an..4)
     */
    public function dangerousGoodsLabelMarking2(): string
    {
        return $this->component(1, 10);
    }

    /**
     * C236/8246 (an..4)
     */
    public function dangerousGoodsLabelMarking3(): string
    {
        return $this->component(2, 10);
    }

    /**
     * 8255 (an..3)
     */
    public function packingInstructionCoded(): string
    {
        return $this->element(11);
    }

    /**
     * 8325 (an..3)
     */
    public function categoryOfMeansOfTransportCoded(): string
    {
        return $this->element(12);
    }

    /**
     * 8211 (an..3)
     */
    public function permissionForTransportCoded(): string
    {
        return $this->element(13);
    }
}
