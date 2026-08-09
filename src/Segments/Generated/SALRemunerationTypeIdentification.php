<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * remunerationTypeIdentification — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class SALRemunerationTypeIdentification extends AbstractSegment
{
    public function tag(): string
    {
        return 'SAL';
    }

    /**
     * C049/5315 (an..3)
     */
    public function remunerationTypeCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C049/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C049/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C049/5314 (an..35)
     */
    public function remunerationType(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C049/5314 (an..35)
     */
    public function remunerationType2(): string
    {
        return $this->component(4, 1);
    }
}
