<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * physicalSampleDescription — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class PSDPhysicalSampleDescription extends AbstractSegment
{
    public function tag(): string
    {
        return 'PSD';
    }

    /**
     * 4407 (an..3)
     */
    public function sampleProcessStatusCoded(): string
    {
        return $this->element(1);
    }

    /**
     * 7039 (an..3)
     */
    public function sampleSelectionMethodCoded(): string
    {
        return $this->element(2);
    }

    /**
     * C526/6071 (an..3)
     */
    public function frequencyQualifier(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C526/6072 (n..9)
     */
    public function frequencyValue(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C526/6411 (an..3)
     */
    public function measureUnitQualifier(): string
    {
        return $this->component(2, 3);
    }

    /**
     * 7045 (an..3)
     */
    public function sampleDescriptionCoded(): string
    {
        return $this->element(4);
    }

    /**
     * 7047 (an..3)
     */
    public function sampleDirectionCoded(): string
    {
        return $this->element(5);
    }

    /**
     * C514/3237 (an..3)
     */
    public function sampleLocationCoded(): string
    {
        return $this->firstComponent(6);
    }

    /**
     * C514/3236 (an..35)
     */
    public function sampleLocation(): string
    {
        return $this->component(1, 6);
    }

    /**
     * C514/3237 (an..3)
     */
    public function sampleLocationCoded2(): string
    {
        return $this->firstComponent(7);
    }

    /**
     * C514/3236 (an..35)
     */
    public function sampleLocation2(): string
    {
        return $this->component(1, 7);
    }

    /**
     * C514/3237 (an..3)
     */
    public function sampleLocationCoded3(): string
    {
        return $this->firstComponent(8);
    }

    /**
     * C514/3236 (an..35)
     */
    public function sampleLocation3(): string
    {
        return $this->component(1, 8);
    }
}
