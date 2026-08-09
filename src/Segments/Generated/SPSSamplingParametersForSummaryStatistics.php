<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * samplingParametersForSummaryStatistics — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class SPSSamplingParametersForSummaryStatistics extends AbstractSegment
{
    public function tag(): string
    {
        return 'SPS';
    }

    /**
     * C526/6071 (an..3)
     */
    public function frequencyQualifier(): string
    {
        return $this->firstComponent(1);
    }

    /**
     * C526/6072 (n..9)
     */
    public function frequencyValue(): string
    {
        return $this->component(1, 1);
    }

    /**
     * C526/6411 (an..3)
     */
    public function measureUnitQualifier(): string
    {
        return $this->component(2, 1);
    }

    /**
     * 6074 (n..6)
     */
    public function confidenceLimit(): string
    {
        return $this->element(2);
    }

    /**
     * C512/6173 (an..3)
     */
    public function sizeQualifier(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C512/6174 (n..15)
     */
    public function size(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C512/6173 (an..3)
     */
    public function sizeQualifier2(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C512/6174 (n..15)
     */
    public function size2(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C512/6173 (an..3)
     */
    public function sizeQualifier3(): string
    {
        return $this->firstComponent(5);
    }

    /**
     * C512/6174 (n..15)
     */
    public function size3(): string
    {
        return $this->component(1, 5);
    }

    /**
     * C512/6173 (an..3)
     */
    public function sizeQualifier4(): string
    {
        return $this->firstComponent(6);
    }

    /**
     * C512/6174 (n..15)
     */
    public function size4(): string
    {
        return $this->component(1, 6);
    }

    /**
     * C512/6173 (an..3)
     */
    public function sizeQualifier5(): string
    {
        return $this->firstComponent(7);
    }

    /**
     * C512/6174 (n..15)
     */
    public function size5(): string
    {
        return $this->component(1, 7);
    }
}
