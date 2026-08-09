<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * handlingInstructions — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class HANHandlingInstructions extends AbstractSegment
{
    public function tag(): string
    {
        return 'HAN';
    }

    /**
     * C524/4079 (an..3)
     */
    public function handlingInstructionsCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C524/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C524/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C524/4078 (an..70)
     */
    public function handlingInstructions(): string
    {
        return $this->component(3, 1);
    }

    /**
     * C218/7419 (an..4)
     */
    public function hazardousMaterialClassCodeIdentification(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C218/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C218/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 2);
    }
}
