<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * nationality — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class NATNationality extends AbstractSegment
{
    public function tag(): string
    {
        return 'NAT';
    }

    /**
     * 3493 (an..3)
     */
    public function nationalityQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C042/3293 (an..3)
     */
    public function nationalityCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C042/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C042/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C042/3292 (a..35)
     */
    public function nationality(): string
    {
        return $this->component(3, 2);
    }
}
