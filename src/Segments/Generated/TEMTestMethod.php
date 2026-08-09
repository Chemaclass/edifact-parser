<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * testMethod — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class TEMTestMethod extends AbstractSegment
{
    public function tag(): string
    {
        return 'TEM';
    }

    /**
     * C244/4415 (an..17)
     */
    public function testMethodIdentification(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C244/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C244/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C244/4416 (an..70)
     */
    public function testDescription(): string
    {
        return $this->component(3, 1);
    }

    /**
     * 4419 (an..3)
     */
    public function testRouteOfAdministeringCoded(): string
    {
        return $this->element(2);
    }

    /**
     * 3077 (an..3)
     */
    public function testMediaCoded(): string
    {
        return $this->element(3);
    }

    /**
     * 6311 (an..3)
     */
    public function measurementApplicationQualifier(): string
    {
        return $this->element(4);
    }

    /**
     * 7188 (an..30)
     */
    public function testRevisionNumber(): string
    {
        return $this->element(5);
    }

    /**
     * C515/4425 (an..17)
     */
    public function testReasonIdentification(): string
    {
        return $this->firstComponent(6);
    }

    /**
     * C515/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 6);
    }

    /**
     * C515/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 6);
    }

    /**
     * C515/4424 (an..35)
     */
    public function testReason(): string
    {
        return $this->component(3, 6);
    }
}
