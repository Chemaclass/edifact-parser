<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * natureOfCargo — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class GDSNatureOfCargo extends AbstractSegment
{
    public function tag(): string
    {
        return 'GDS';
    }

    /**
     * C703/7085 (an..3)
     */
    public function natureOfCargoCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C703/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C703/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }
}
