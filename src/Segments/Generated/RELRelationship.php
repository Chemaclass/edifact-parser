<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * relationship — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class RELRelationship extends AbstractSegment
{
    public function tag(): string
    {
        return 'REL';
    }

    /**
     * 9141 (an..3)
     */
    public function relationshipQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C941/9143 (an..3)
     */
    public function relationshipCoded(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C941/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C941/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C941/9142 (an..35)
     */
    public function relationship(): string
    {
        return $this->component(3, 2);
    }
}
