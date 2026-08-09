<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * generalIndicator — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class GISGeneralIndicator extends AbstractSegment
{
    public function tag(): string
    {
        return 'GIS';
    }

    /**
     * C529/7365 (an..3)
     */
    public function processingIndicatorCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C529/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C529/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C529/7187 (an..17)
     */
    public function processTypeIdentification(): string
    {
        return $this->component(3, 1);
    }
}
