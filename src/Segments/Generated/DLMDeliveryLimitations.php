<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Generated;

use EdifactParser\Segments\AbstractSegment;

/**
 * deliveryLimitations — generated from UN/EDIFACT D96A.
 *
 * Do not edit by hand: run `php tools/generate-segments.php D96A` instead.
 *
 * @psalm-immutable
 */
final class DLMDeliveryLimitations extends AbstractSegment
{
    public function tag(): string
    {
        return 'DLM';
    }

    /**
     * 4455 (an..3)
     */
    public function backOrderCoded(): string
    {
        return $this->element(1);
    }

    /**
     * C522/4403 (an..3)
     */
    public function instructionQualifier(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * C522/4401 (an..3)
     */
    public function instructionCoded(): string
    {
        return $this->component(1, 2);
    }

    /**
     * C522/1131 (an..3)
     */
    public function codeListQualifier(): string
    {
        return $this->component(2, 2);
    }

    /**
     * C522/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded(): string
    {
        return $this->component(3, 2);
    }

    /**
     * C522/4400 (an..35)
     */
    public function instruction(): string
    {
        return $this->component(4, 2);
    }

    /**
     * C214/7161 (an..3)
     */
    public function specialServicesCoded(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * C214/1131 (an..3)
     */
    public function codeListQualifier2(): string
    {
        return $this->component(1, 3);
    }

    /**
     * C214/3055 (an..3)
     */
    public function codeListResponsibleAgencyCoded2(): string
    {
        return $this->component(2, 3);
    }

    /**
     * C214/7160 (an..35)
     */
    public function specialService(): string
    {
        return $this->component(3, 3);
    }

    /**
     * C214/7160 (an..35)
     */
    public function specialService2(): string
    {
        return $this->component(4, 3);
    }

    /**
     * 4457 (an..3)
     */
    public function productserviceSubstitutionCoded(): string
    {
        return $this->element(4);
    }
}
