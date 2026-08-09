<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * status — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class STSStatus extends AbstractSegment
{
    public function tag(): string
    {
        return 'STS';
    }

    /**
     * C601/9015 (an..3)
     */
    public function statusTypeCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C601/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C601/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C555/9011 (an..3)
     */
    public function statusEventCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C555/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C555/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C555/9010 (an..35)
     */
    public function statusEvent(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C556/9013 (an..3)
     */
    public function statusReasonCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C556/1131 (an..3)
     */
    public function codeListQualifier3(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C556/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded3(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C556/9012 (an..35)
     */
    public function statusReason(): string
    {
        return $this->component(3, 3);
    }

    /**
     * C556/9013 (an..3)
     */
    public function statusReasonCoded2(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C556/1131 (an..3)
     */
    public function codeListQualifier4(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C556/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded4(): string
    {
        return $this->component(2, 4);
    }

    /**
     * C556/9012 (an..35)
     */
    public function statusReason2(): string
    {
        return $this->component(3, 4);
    }

    /**
     * C556/9013 (an..3)
     */
    public function statusReasonCoded3(): string
    {
        return $this->firstComponent(5);
    }

    /**
     * C556/1131 (an..3)
     */
    public function codeListQualifier5(): string
    {
        return $this->component(1, 5);
    }

    /**
     * C556/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded5(): string
    {
        return $this->component(2, 5);
    }

    /**
     * C556/9012 (an..35)
     */
    public function statusReason3(): string
    {
        return $this->component(3, 5);
    }

    /**
     * C556/9013 (an..3)
     */
    public function statusReasonCoded4(): string
    {
        return $this->firstComponent(6);
    }

    /**
     * C556/1131 (an..3)
     */
    public function codeListQualifier6(): string
    {
        return $this->component(1, 6);
    }

    /**
     * C556/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded6(): string
    {
        return $this->component(2, 6);
    }

    /**
     * C556/9012 (an..35)
     */
    public function statusReason4(): string
    {
        return $this->component(3, 6);
    }

    /**
     * C556/9013 (an..3)
     */
    public function statusReasonCoded5(): string
    {
        return $this->firstComponent(7);
    }

    /**
     * C556/1131 (an..3)
     */
    public function codeListQualifier7(): string
    {
        return $this->component(1, 7);
    }

    /**
     * C556/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded7(): string
    {
        return $this->component(2, 7);
    }

    /**
     * C556/9012 (an..35)
     */
    public function statusReason5(): string
    {
        return $this->component(3, 7);
    }
}
