<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * governmentalRequirements — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class GORGovernmentalRequirements extends AbstractSegment
{
    public function tag(): string
    {
        return 'GOR';
    }

    /**
     * 8323 (an..3)
     */
    public function transportMovementCoded(): string
    {
        return $this->element(1);
    }

    /**
     * C232/9415 (an..3)
     */
    public function governmentAgencyCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C232/9411 (an..3)
     */
    public function governmentInvolvementCoded(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C232/9417 (an..3)
     */
    public function governmentActionCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C232/9353 (an..3)
     */
    public function governmentProcedureCoded(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C232/9415 (an..3)
     */
    public function governmentAgencyCoded2(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C232/9411 (an..3)
     */
    public function governmentInvolvementCoded2(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C232/9417 (an..3)
     */
    public function governmentActionCoded2(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C232/9353 (an..3)
     */
    public function governmentProcedureCoded2(): string
    {
        return $this->component(3, 3);
    }

    /**
     * C232/9415 (an..3)
     */
    public function governmentAgencyCoded3(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C232/9411 (an..3)
     */
    public function governmentInvolvementCoded3(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C232/9417 (an..3)
     */
    public function governmentActionCoded3(): string
    {
        return $this->component(2, 4);
    }

    /**
     * C232/9353 (an..3)
     */
    public function governmentProcedureCoded3(): string
    {
        return $this->component(3, 4);
    }

    /**
     * C232/9415 (an..3)
     */
    public function governmentAgencyCoded4(): string
    {
        return $this->firstComponent(5);
    }

    /**
     * C232/9411 (an..3)
     */
    public function governmentInvolvementCoded4(): string
    {
        return $this->component(1, 5);
    }

    /**
     * C232/9417 (an..3)
     */
    public function governmentActionCoded4(): string
    {
        return $this->component(2, 5);
    }

    /**
     * C232/9353 (an..3)
     */
    public function governmentProcedureCoded4(): string
    {
        return $this->component(3, 5);
    }
}
