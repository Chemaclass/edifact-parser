<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * segmentUsageDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class SGUSegmentUsageDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'SGU';
    }

    /**
     * 9166 (an..3)
     */
    public function segmentTag(): string
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
     * 7168 (n)
     */
    public function levelNumber(): string
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

    /**
     * 1049 (an..3)
     */
    public function messageSectionCoded(): string
    {
        return $this->element(6);
    }

    /**
     * 4513 (an..3)
     */
    public function maintenanceOperationCoded(): string
    {
        return $this->element(7);
    }
}
