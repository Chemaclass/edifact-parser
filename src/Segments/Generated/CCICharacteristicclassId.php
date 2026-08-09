<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * characteristicclassId — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CCICharacteristicclassId extends AbstractSegment
{
    public function tag(): string
    {
        return 'CCI';
    }

    /**
     * 7059 (an..3)
     */
    public function propertyClassCoded(): string
    {
        return $this->element(1);
    }

    /**
     * C502/6313 (an..3)
     */
    public function measurementDimensionCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C502/6321 (an..3)
     */
    public function measurementSignificanceCoded(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C502/6155 (an..3)
     */
    public function measurementAttributeCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C502/6154 (an..70)
     */
    public function measurementAttribute(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C240/7037 (an..17)
     */
    public function characteristicIdentification(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C240/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C240/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C240/7036 (an..35)
     */
    public function characteristic(): string
    {
        return $this->component(3, 3);
    }

    /**
     * C240/7036 (an..35)
     */
    public function characteristic2(): string
    {
        return $this->component(4, 3);
    }
}
