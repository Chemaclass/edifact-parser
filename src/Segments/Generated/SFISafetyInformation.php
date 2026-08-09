<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * safetyInformation — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class SFISafetyInformation extends AbstractSegment
{
    public function tag(): string
    {
        return 'SFI';
    }

    /**
     * 7164 (an..12)
     */
    public function hierarchicalIdNumber(): string
    {
        return $this->element(1);
    }

    /**
     * C814/4046 (n..2)
     */
    public function safetySection(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C814/4044 (an..70)
     */
    public function safetySectionName(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C815/4039 (an..3)
     */
    public function additionalSafetyInformationCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C815/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C815/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C815/4038 (an..35)
     */
    public function additionalSafetyInformation(): string
    {
        return $this->component(3, 3);
    }

    /**
     * 4513 (an..3)
     */
    public function maintenanceOperationCoded(): string
    {
        return $this->element(4);
    }
}
