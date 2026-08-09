<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * quantityVariances — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class QVRQuantityVariances extends AbstractSegment
{
    public function tag(): string
    {
        return 'QVR';
    }

    /**
     * C279/6064 (n..15)
     */
    public function quantityDifference(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C279/6063 (an..3)
     */
    public function quantityQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * 4221 (an..3)
     */
    public function discrepancyCoded(): string
    {
        return $this->element(2);
    }

    /**
     * C960/4295 (an..3)
     */
    public function changeReasonCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C960/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C960/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C960/4294 (an..35)
     */
    public function changeReason(): string
    {
        return $this->component(3, 3);
    }
}
