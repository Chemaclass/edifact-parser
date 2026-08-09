<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * segmentIdentification — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class SEGSegmentIdentification extends AbstractSegment
{
    public function tag(): string
    {
        return 'SEG';
    }

    /**
     * 9166 (an..3)
     */
    public function segmentTag(): string
    {
        return $this->element(1);
    }

    /**
     * 1507 (an..3)
     */
    public function classDesignatorCoded(): string
    {
        return $this->element(2);
    }

    /**
     * 4513 (an..3)
     */
    public function maintenanceOperationCoded(): string
    {
        return $this->element(3);
    }
}
