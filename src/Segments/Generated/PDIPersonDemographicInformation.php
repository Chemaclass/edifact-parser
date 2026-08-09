<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * personDemographicInformation — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class PDIPersonDemographicInformation extends AbstractSegment
{
    public function tag(): string
    {
        return 'PDI';
    }

    /**
     * 3499 (an..3)
     */
    public function sexCoded(): string
    {
        return $this->element(1);
    }

    /**
     * C085/3479 (an..3)
     */
    public function maritalStatusCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C085/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C085/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C085/3478 (an..35)
     */
    public function maritalStatus(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C101/3483 (an..3)
     */
    public function religionCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C101/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C101/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C101/3482 (an..35)
     */
    public function religion(): string
    {
        return $this->component(3, 3);
    }
}
