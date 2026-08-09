<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * partyName — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class PNAPartyName extends AbstractSegment
{
    public function tag(): string
    {
        return 'PNA';
    }

    /**
     * 3035 (an..3)
     */
    public function partyQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C206/7402 (an..35)
     */
    public function identityNumber(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C206/7405 (an..3)
     */
    public function identityNumberQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C206/4405 (an..3)
     */
    public function statusCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C082/3039 (an..35)
     */
    public function partyIdIdentification(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C082/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C082/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 3);
    }

    /**
     * 3403 (an..3)
     */
    public function nameTypeCoded(): string
    {
        return $this->element(4);
    }

    /**
     * 3397 (an..3)
     */
    public function nameStatusCoded(): string
    {
        return $this->element(5);
    }

    /**
     * C816/3405 (an..3)
     */
    public function nameComponentQualifier(): string
    {
        return $this->firstComponent(6);
    }

    /**
     * C816/3398 (an..70)
     */
    public function nameComponent(): string
    {
        return $this->component(1, 6);
    }

    /**
     * C816/3401 (an..3)
     */
    public function nameComponentStatusCoded(): string
    {
        return $this->component(2, 6);
    }

    /**
     * C816/3295 (an..3)
     */
    public function nameComponentOriginalRepresentationCoded(): string
    {
        return $this->component(3, 6);
    }

    /**
     * C816/3405 (an..3)
     */
    public function nameComponentQualifier2(): string
    {
        return $this->firstComponent(7);
    }

    /**
     * C816/3398 (an..70)
     */
    public function nameComponent2(): string
    {
        return $this->component(1, 7);
    }

    /**
     * C816/3401 (an..3)
     */
    public function nameComponentStatusCoded2(): string
    {
        return $this->component(2, 7);
    }

    /**
     * C816/3295 (an..3)
     */
    public function nameComponentOriginalRepresentationCoded2(): string
    {
        return $this->component(3, 7);
    }

    /**
     * C816/3405 (an..3)
     */
    public function nameComponentQualifier3(): string
    {
        return $this->firstComponent(8);
    }

    /**
     * C816/3398 (an..70)
     */
    public function nameComponent3(): string
    {
        return $this->component(1, 8);
    }

    /**
     * C816/3401 (an..3)
     */
    public function nameComponentStatusCoded3(): string
    {
        return $this->component(2, 8);
    }

    /**
     * C816/3295 (an..3)
     */
    public function nameComponentOriginalRepresentationCoded3(): string
    {
        return $this->component(3, 8);
    }

    /**
     * C816/3405 (an..3)
     */
    public function nameComponentQualifier4(): string
    {
        return $this->firstComponent(9);
    }

    /**
     * C816/3398 (an..70)
     */
    public function nameComponent4(): string
    {
        return $this->component(1, 9);
    }

    /**
     * C816/3401 (an..3)
     */
    public function nameComponentStatusCoded4(): string
    {
        return $this->component(2, 9);
    }

    /**
     * C816/3295 (an..3)
     */
    public function nameComponentOriginalRepresentationCoded4(): string
    {
        return $this->component(3, 9);
    }

    /**
     * C816/3405 (an..3)
     */
    public function nameComponentQualifier5(): string
    {
        return $this->firstComponent(10);
    }

    /**
     * C816/3398 (an..70)
     */
    public function nameComponent5(): string
    {
        return $this->component(1, 10);
    }

    /**
     * C816/3401 (an..3)
     */
    public function nameComponentStatusCoded5(): string
    {
        return $this->component(2, 10);
    }

    /**
     * C816/3295 (an..3)
     */
    public function nameComponentOriginalRepresentationCoded5(): string
    {
        return $this->component(3, 10);
    }
}
