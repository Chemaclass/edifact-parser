<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * dataElementUsageDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class ELUDataElementUsageDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'ELU';
    }

    /**
     * 9162 (an..4)
     */
    public function dataElementTag(): string
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
     * 1050 (an..6)
     */
    public function sequenceNumber(): string
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
}
