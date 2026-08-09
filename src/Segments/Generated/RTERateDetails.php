<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * rateDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class RTERateDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'RTE';
    }

    /**
     * C128/5419 (an..3)
     */
    public function rateTypeQualifier(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C128/5420 (n..15)
     */
    public function ratePerUnit(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C128/5284 (n..9)
     */
    public function unitPriceBasis(): string
    {
        return $this->component(2, 1);
    }

    /**
     * C128/6411 (an..3)
     */
    public function measureUnitQualifier(): string
    {
        return $this->component(3, 1);
    }
}
