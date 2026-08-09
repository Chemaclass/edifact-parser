<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * relatedIdentificationNumbers — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class GIRRelatedIdentificationNumbers extends AbstractSegment
{
    public function tag(): string
    {
        return 'GIR';
    }

    /**
     * 7297 (an..3)
     */
    public function setIdentificationQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * C206/7402 (an..35)
     */
    public function identityNumber(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C206/7405 (an..3)
     */
    public function identityNumberQualifier(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C206/4405 (an..3)
     */
    public function statusCoded(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C206/7402 (an..35)
     */
    public function identityNumber2(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C206/7405 (an..3)
     */
    public function identityNumberQualifier2(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C206/4405 (an..3)
     */
    public function statusCoded2(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C206/7402 (an..35)
     */
    public function identityNumber3(): string
    {
        return $this->firstComponent(4);
    }

    /**
     * C206/7405 (an..3)
     */
    public function identityNumberQualifier3(): string
    {
        return $this->component(1, 4);
    }

    /**
     * C206/4405 (an..3)
     */
    public function statusCoded3(): string
    {
        return $this->component(2, 4);
    }

    /**
     * C206/7402 (an..35)
     */
    public function identityNumber4(): string
    {
        return $this->firstComponent(5);
    }

    /**
     * C206/7405 (an..3)
     */
    public function identityNumberQualifier4(): string
    {
        return $this->component(1, 5);
    }

    /**
     * C206/4405 (an..3)
     */
    public function statusCoded4(): string
    {
        return $this->component(2, 5);
    }

    /**
     * C206/7402 (an..35)
     */
    public function identityNumber5(): string
    {
        return $this->firstComponent(6);
    }

    /**
     * C206/7405 (an..3)
     */
    public function identityNumberQualifier5(): string
    {
        return $this->component(1, 6);
    }

    /**
     * C206/4405 (an..3)
     */
    public function statusCoded5(): string
    {
        return $this->component(2, 6);
    }
}
