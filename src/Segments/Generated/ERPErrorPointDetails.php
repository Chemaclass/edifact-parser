<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * errorPointDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class ERPErrorPointDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'ERP';
    }

    /**
     * C701/1049 (an..3)
     */
    public function messageSectionCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C701/1052 (an..35)
     */
    public function messageItemNumber(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C701/1054 (n..6)
     */
    public function messageSubitemNumber(): string
    {
        return $this->component(2, 1);
    }
}
