<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * sealNumber — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class SELSealNumber extends AbstractSegment
{
    public function tag(): string
    {
        return 'SEL';
    }

    /**
     * 9308 (an..10)
     */
    public function sealNumber(): string
    {
        return $this->element(1);
    }

    /**
     * C215/9303 (an..3)
     */
    public function sealingPartyCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C215/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C215/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C215/9302 (an..35)
     */
    public function sealingParty(): string
    {
        return $this->component(3, 2);
    }

    /**
     * 4517 (an..3)
     */
    public function sealConditionCoded(): string
    {
        return $this->element(3);
    }
}
