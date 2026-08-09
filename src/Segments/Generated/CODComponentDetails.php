<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * componentDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CODComponentDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'COD';
    }

    /**
     * C823/7505 (an..3)
     */
    public function typeOfUnitcomponentCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C823/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C823/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C823/7504 (an..35)
     */
    public function typeOfUnitcomponent(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C824/7507 (an..3)
     */
    public function componentMaterialCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C824/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C824/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C824/7506 (an..35)
     */
    public function componentMaterial(): string
    {
        return $this->component(3, 2);
    }
}
