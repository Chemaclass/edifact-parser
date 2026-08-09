<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * applicationErrorInformation — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class ERCApplicationErrorInformation extends AbstractSegment
{
    public function tag(): string
    {
        return 'ERC';
    }

    /**
     * C901/9321 (an..8)
     */
    public function applicationErrorIdentification(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C901/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C901/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 1);
    }
}
