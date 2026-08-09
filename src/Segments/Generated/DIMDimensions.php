<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * dimensions — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class DIMDimensions extends AbstractSegment
{
    public function tag(): string
    {
        return 'DIM';
    }

    /**
     * 6145 (an..3)
     */
    public function dimensionQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C211/6411 (an..3)
     */
    public function measureUnitQualifier(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C211/6168 (n..15)
     */
    public function lengthDimension(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C211/6140 (n..15)
     */
    public function widthDimension(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C211/6008 (n..15)
     */
    public function heightDimension(): string
    {
        return $this->component(3, 2);
    }
}
