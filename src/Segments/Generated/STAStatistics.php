<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * statistics — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class STAStatistics extends AbstractSegment
{
    public function tag(): string
    {
        return 'STA';
    }

    /**
     * 6331 (an..3)
     */
    public function statisticTypeCoded(): string
    {
        return $this->element(1);
    }

    /**
     * C527/6314 (n..18)
     */
    public function measurementValue(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C527/6411 (an..3)
     */
    public function measureUnitQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C527/6313 (an..3)
     */
    public function measurementDimensionCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C527/6321 (an..3)
     */
    public function measurementSignificanceCoded(): string
    {
        return $this->component(3, 2);
    }
}
