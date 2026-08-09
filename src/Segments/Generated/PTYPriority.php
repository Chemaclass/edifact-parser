<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * priority — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class PTYPriority extends AbstractSegment
{
    public function tag(): string
    {
        return 'PTY';
    }

    /**
     * 4035 (an..3)
     */
    public function priorityQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C585/4037 (an..3)
     */
    public function priorityCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C585/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C585/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C585/4036 (an..35)
     */
    public function priority(): string
    {
        return $this->component(3, 2);
    }
}
