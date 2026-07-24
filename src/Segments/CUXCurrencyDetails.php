<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class CUXCurrencyDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'CUX';
    }

    public function subId(): string
    {
        return $this->requiredSubId();
    }

    /**
     * Currency usage qualifier (C504/6347), e.g. '2' = reference currency,
     * '3' = target currency.
     */
    public function usageQualifier(): string
    {
        return $this->component(0);
    }

    /**
     * Currency code, ISO 4217 (C504/6345): CUX+2:EUR:4 -> 'EUR'.
     */
    public function currencyCode(): string
    {
        return $this->component(1);
    }

    /**
     * Currency type/rate qualifier (C504/6343): CUX+2:EUR:4 -> '4'.
     */
    public function rateQualifier(): string
    {
        return $this->component(2);
    }
}
