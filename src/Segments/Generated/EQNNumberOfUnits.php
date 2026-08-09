<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * numberOfUnits — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class EQNNumberOfUnits extends AbstractSegment
{
    public function tag(): string
    {
        return 'EQN';
    }

    /**
     * C523/6350 (n..15)
     */
    public function numberOfUnits(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C523/6353 (an..3)
     */
    public function numberOfUnitsQualifier(): string
    {
        return $this->component(1, 1);
    }
}
