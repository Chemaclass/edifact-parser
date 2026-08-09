<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * consignmentPackingSequence — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class CPSConsignmentPackingSequence extends AbstractSegment
{
    public function tag(): string
    {
        return 'CPS';
    }

    /**
     * 7164 (an..12)
     */
    public function hierarchicalIdNumber(): string
    {
        return $this->element(1);
    }

    /**
     * 7166 (an..12)
     */
    public function hierarchicalParentId(): string
    {
        return $this->element(2);
    }

    /**
     * 7075 (an..3)
     */
    public function packagingLevelCoded(): string
    {
        return $this->element(3);
    }
}
