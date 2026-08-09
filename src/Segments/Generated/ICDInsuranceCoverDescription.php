<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * insuranceCoverDescription — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class ICDInsuranceCoverDescription extends AbstractSegment
{
    public function tag(): string
    {
        return 'ICD';
    }

    /**
     * C330/4497 (an..3)
     */
    public function insuranceCoverTypeCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C330/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C330/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C331/4495 (an..17)
     */
    public function insuranceCoverIdentification(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C331/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C331/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C331/4494 (an..35)
     */
    public function insuranceCover(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C331/4494 (an..35)
     */
    public function insuranceCover2(): string
    {
        return $this->component(4, 2);
    }
}
