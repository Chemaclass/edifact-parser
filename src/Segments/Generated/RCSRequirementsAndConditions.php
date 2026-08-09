<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * requirementsAndConditions — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class RCSRequirementsAndConditions extends AbstractSegment
{
    public function tag(): string
    {
        return 'RCS';
    }

    /**
     * 7293 (an..3)
     */
    public function sectorsubjectIdentificationQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C550/7295 (an..17)
     */
    public function requirementconditionIdentification(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C550/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C550/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C550/7294 (an..35)
     */
    public function requirementOrCondition(): string
    {
        return $this->component(3, 2);
    }

    /**
     * 1229 (an..3)
     */
    public function actionRequestnotificationCoded(): string
    {
        return $this->element(3);
    }
}
