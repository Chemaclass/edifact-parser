<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * characteristicValue — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CAVCharacteristicValue extends AbstractSegment
{
    public function tag(): string
    {
        return 'CAV';
    }

    /**
     * C889/7111 (an..3)
     */
    public function characteristicValueCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C889/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C889/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C889/7110 (an..35)
     */
    public function characteristicValue(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C889/7110 (an..35)
     */
    public function characteristicValue2(): string
    {
        return $this->component(4, 1);
    }
}
