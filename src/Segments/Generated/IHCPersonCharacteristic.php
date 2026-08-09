<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * personCharacteristic — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class IHCPersonCharacteristic extends AbstractSegment
{
    public function tag(): string
    {
        return 'IHC';
    }

    /**
     * 3289 (an..3)
     */
    public function personCharacteristicQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C818/3311 (an..8)
     */
    public function personInheritedCharacteristicIdentification(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C818/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C818/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C818/3310 (an..70)
     */
    public function personInheritedCharacteristic(): string
    {
        return $this->component(3, 2);
    }
}
