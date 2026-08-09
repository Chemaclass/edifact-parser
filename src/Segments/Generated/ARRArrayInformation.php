<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * arrayInformation — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class ARRArrayInformation extends AbstractSegment
{
    public function tag(): string
    {
        return 'ARR';
    }

    /**
     * C778/7164 (an..12)
     */
    public function hierarchicalIdNumber(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C778/1050 (an..6)
     */
    public function sequenceNumber(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C770/9424 (an..35)
     */
    public function arrayCellInformation(): string
    {
        return $this->firstComponent(2);
    }
}
