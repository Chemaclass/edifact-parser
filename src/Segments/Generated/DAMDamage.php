<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * damage — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class DAMDamage extends AbstractSegment
{
    public function tag(): string
    {
        return 'DAM';
    }

    /**
     * 7493 (an..3)
     */
    public function damageDetailsQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C821/7501 (an..3)
     */
    public function typeOfDamageCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C821/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C821/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C821/7500 (an..35)
     */
    public function typeOfDamage(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C822/7503 (an..4)
     */
    public function damageAreaIdentification(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C822/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C822/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C822/7502 (an..35)
     */
    public function damageArea(): string
    {
        return $this->component(3, 3);
    }

    /**
     * C825/7509 (an..3)
     */
    public function damageSeverityCoded(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C825/1131 (an..3)
     */
    public function codeListQualifier3(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C825/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded3(): string
    {
        return $this->component(2, 4);
    }

    /**
     * C825/7508 (an..35)
     */
    public function damageSeverity(): string
    {
        return $this->component(3, 4);
    }

    /**
     * C826/1229 (an..3)
     */
    public function actionRequestnotificationCoded(): string
    {
        return $this->firstComponent(5);
    }

    /**
     * C826/1131 (an..3)
     */
    public function codeListQualifier4(): string
    {
        return $this->component(1, 5);
    }

    /**
     * C826/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded4(): string
    {
        return $this->component(2, 5);
    }

    /**
     * C826/1228 (an..35)
     */
    public function actionRequestnotification(): string
    {
        return $this->component(3, 5);
    }
}
