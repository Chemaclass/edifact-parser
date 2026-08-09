<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * physicalOrLogicalState — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CDIPhysicalOrLogicalState extends AbstractSegment
{
    public function tag(): string
    {
        return 'CDI';
    }

    /**
     * 7001 (an..3)
     */
    public function physicalOrLogicalStateQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C564/7007 (an..3)
     */
    public function physicalOrLogicalStateCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C564/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C564/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C564/7006 (an..35)
     */
    public function physicalOrLogicalState(): string
    {
        return $this->component(3, 2);
    }
}
