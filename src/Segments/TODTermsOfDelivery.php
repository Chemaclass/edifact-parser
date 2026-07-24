<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class TODTermsOfDelivery extends AbstractSegment
{
    public function tag(): string
    {
        return 'TOD';
    }

    /**
     * Delivery or transport terms function code (4055), e.g. '6' = terms of
     * delivery.
     */
    public function functionCode(): string
    {
        return $this->element(1);
    }

    /**
     * Transport charges method of payment (4215), e.g. 'CC' = collect,
     * 'PP' = prepaid.
     */
    public function transportChargesMethod(): string
    {
        return $this->element(2);
    }

    /**
     * Delivery terms code, e.g. Incoterm (C100/4053):
     * TOD+6++CIF -> 'CIF'.
     */
    public function termsCode(): string
    {
        return $this->firstComponent(3);
    }
}
