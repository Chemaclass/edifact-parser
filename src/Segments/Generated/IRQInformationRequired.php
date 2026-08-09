<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * informationRequired — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class IRQInformationRequired extends AbstractSegment
{
    public function tag(): string
    {
        return 'IRQ';
    }

    /**
     * C333/4511 (an..3)
     */
    public function requestedInformationCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C333/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C333/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C333/4510 (an..35)
     */
    public function requestedInformation(): string
    {
        return $this->component(3, 1);
    }
}
