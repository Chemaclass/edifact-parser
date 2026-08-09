<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * address — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class ADRAddress extends AbstractSegment
{
    public function tag(): string
    {
        return 'ADR';
    }

    /**
     * C817/3299 (an..3)
     */
    public function addressPurposeCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C817/3131 (an..3)
     */
    public function addressTypeCoded(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C817/3475 (an..3)
     */
    public function addressStatusCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C090/3477 (an..3)
     */
    public function addressFormatCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C090/3286 (an..70)
     */
    public function addressComponent(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C090/3286 (an..70)
     */
    public function addressComponent2(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C090/3286 (an..70)
     */
    public function addressComponent3(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C090/3286 (an..70)
     */
    public function addressComponent4(): string
    {
        return $this->component(4, 2);
    }

    /**
     * C090/3286 (an..70)
     */
    public function addressComponent5(): string
    {
        return $this->component(5, 2);
    }

    /**
     * 3164 (an..35)
     */
    public function cityName(): string
    {
        return $this->element(3);
    }

    /**
     * 3251 (an..9)
     */
    public function postcodeIdentification(): string
    {
        return $this->element(4);
    }

    /**
     * 3207 (an..3)
     */
    public function countryCoded(): string
    {
        return $this->element(5);
    }

    /**
     * C819/3229 (an..9)
     */
    public function countrySubentityIdentification(): string
    {
        return $this->firstComponent(6);
    }

    /**
     * C819/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 6);
    }

    /**
     * C819/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 6);
    }

    /**
     * C819/3228 (an..35)
     */
    public function countrySubentity(): string
    {
        return $this->component(3, 6);
    }

    /**
     * C517/3225 (an..25)
     */
    public function placelocationIdentification(): string
    {
        return $this->firstComponent(7);
    }

    /**
     * C517/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 7);
    }

    /**
     * C517/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 7);
    }

    /**
     * C517/3224 (an..70)
     */
    public function placelocation(): string
    {
        return $this->component(3, 7);
    }
}
