<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * rangeDetails — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class RNGRangeDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'RNG';
    }

    /**
     * 6167 (an..3)
     */
    public function rangeTypeQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C280/6411 (an..3)
     */
    public function measureUnitQualifier(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C280/6162 (n..18)
     */
    public function rangeMinimum(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C280/6152 (n..18)
     */
    public function rangeMaximum(): string
    {
        return $this->component(2, 2);
    }
}
