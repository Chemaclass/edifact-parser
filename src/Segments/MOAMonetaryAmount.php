<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class MOAMonetaryAmount extends AbstractSegment
{
    public function tag(): string
    {
        return 'MOA';
    }

    public function subId(): string
    {
        return $this->requiredSubId();
    }

    /**
     * Monetary amount type qualifier (e.g., '79' = Total line items amount,
     * '125' = Taxable amount, '86' = Message total monetary amount)
     */
    public function amountQualifier(): string
    {
        return $this->component(0);
    }

    /**
     * Monetary amount (raw string)
     */
    public function amount(): string
    {
        return $this->component(1);
    }

    public function amountAsFloat(): float
    {
        return (float) $this->amount();
    }

    /**
     * Currency code (ISO 4217), when present in the composite
     */
    public function currencyCode(): string
    {
        return $this->component(2);
    }
}
