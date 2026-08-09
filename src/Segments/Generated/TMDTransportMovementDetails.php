<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * transportMovementDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class TMDTransportMovementDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'TMD';
    }

    /**
     * C219/8335 (an..3)
     */
    public function movementTypeCoded(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C219/8334 (an..35)
     */
    public function movementType(): string
    {
        return $this->component(1, 1);
    }

    /**
     * 8332 (an..26)
     */
    public function equipmentPlan(): string
    {
        return $this->element(2);
    }

    /**
     * 8341 (an..3)
     */
    public function haulageArrangementsCoded(): string
    {
        return $this->element(3);
    }
}
