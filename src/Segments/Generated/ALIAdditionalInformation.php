<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * additionalInformation — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class ALIAdditionalInformation extends AbstractSegment
{
    public function tag(): string
    {
        return 'ALI';
    }

    /**
     * 3239 (an..3)
     */
    public function countryOfOriginCoded(): string
    {
        return $this->element(1);
    }

    /**
     * 9213 (an..3)
     */
    public function typeOfDutyRegimeCoded(): string
    {
        return $this->element(2);
    }

    /**
     * 4183 (an..3)
     */
    public function specialConditionsCoded(): string
    {
        return $this->element(3);
    }

    /**
     * 4183 (an..3)
     */
    public function specialConditionsCoded2(): string
    {
        return $this->element(4);
    }

    /**
     * 4183 (an..3)
     */
    public function specialConditionsCoded3(): string
    {
        return $this->element(5);
    }

    /**
     * 4183 (an..3)
     */
    public function specialConditionsCoded4(): string
    {
        return $this->element(6);
    }

    /**
     * 4183 (an..3)
     */
    public function specialConditionsCoded5(): string
    {
        return $this->element(7);
    }
}
