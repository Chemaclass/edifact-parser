<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * segmentGroupUsageDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class GRUSegmentGroupUsageDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'GRU';
    }

    /**
     * 9164 (an..4)
     */
    public function groupIdentification(): string
    {
        return $this->element(1);
    }

    /**
     * 7299 (an..3)
     */
    public function requirementDesignatorCoded(): string
    {
        return $this->element(2);
    }

    /**
     * 6176 (n..7)
     */
    public function maximumNumberOfOccurrences(): string
    {
        return $this->element(3);
    }

    /**
     * 4513 (an..3)
     */
    public function maintenanceOperationCoded(): string
    {
        return $this->element(4);
    }

    /**
     * 1050 (an..6)
     */
    public function sequenceNumber(): string
    {
        return $this->element(5);
    }
}
