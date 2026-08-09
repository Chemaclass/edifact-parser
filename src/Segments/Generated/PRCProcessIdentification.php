<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * processIdentification — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class PRCProcessIdentification extends AbstractSegment
{
    public function tag(): string
    {
        return 'PRC';
    }

    /**
     * C242/7187 (an..17)
     */
    public function processTypeIdentification(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C242/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C242/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C242/7186 (an..35)
     */
    public function processType(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C242/7186 (an..35)
     */
    public function processType2(): string
    {
        return $this->component(4, 1);
    }
}
