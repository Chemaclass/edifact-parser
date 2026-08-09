<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * employmentDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class EMPEmploymentDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'EMP';
    }

    /**
     * 9003 (an..3)
     */
    public function employmentQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C948/9005 (an..3)
     */
    public function employmentCategoryCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C948/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C948/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C948/9004 (an..35)
     */
    public function employmentCategory(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C951/9009 (an..3)
     */
    public function occupationCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C951/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C951/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C951/9008 (an..35)
     */
    public function occupation(): string
    {
        return $this->component(3, 3);
    }

    /**
     * C951/9008 (an..35)
     */
    public function occupation2(): string
    {
        return $this->component(4, 3);
    }

    /**
     * C950/9007 (an..3)
     */
    public function qualificationClassificationCoded(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C950/1131 (an..3)
     */
    public function codeListQualifier3(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C950/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded3(): string
    {
        return $this->component(2, 4);
    }

    /**
     * C950/9006 (an..35)
     */
    public function qualificationClassification(): string
    {
        return $this->component(3, 4);
    }

    /**
     * C950/9006 (an..35)
     */
    public function qualificationClassification2(): string
    {
        return $this->component(4, 4);
    }

    /**
     * 3494 (an..35)
     */
    public function jobTitle(): string
    {
        return $this->element(5);
    }

    /**
     * 9035 (an..3)
     */
    public function qualificationAreaCoded(): string
    {
        return $this->element(6);
    }
}
