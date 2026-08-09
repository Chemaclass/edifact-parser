<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * productGroupInformation — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class PGIProductGroupInformation extends AbstractSegment
{
    public function tag(): string
    {
        return 'PGI';
    }

    /**
     * 5379 (an..3)
     */
    public function productGroupTypeCoded(): string
    {
        return $this->element(1);
    }

    /**
     * C288/5389 (an..3)
     */
    public function productGroupCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C288/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C288/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C288/5388 (an..35)
     */
    public function productGroup(): string
    {
        return $this->component(3, 2);
    }
}
