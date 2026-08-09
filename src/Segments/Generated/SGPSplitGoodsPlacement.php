<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * splitGoodsPlacement — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class SGPSplitGoodsPlacement extends AbstractSegment
{
    public function tag(): string
    {
        return 'SGP';
    }

    /**
     * C237/8260 (an..17)
     */
    public function equipmentIdentificationNumber(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C237/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C237/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C237/3207 (an..3)
     */
    public function countryCoded(): string
    {
        return $this->component(3, 1);
    }

    /**
     * 7224 (n..8)
     */
    public function numberOfPackages(): string
    {
        return $this->element(2);
    }
}
