<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Builder;

use EdifactParser\Segments\PRIPrice;
use EdifactParser\Segments\Qualifier\PRIQualifier;

/**
 * Fluent builder for PRI (Price) segments
 */
final class PRIBuilder
{
    private string $qualifier = '';
    private string $price = '';
    private string $priceType = '';

    public function withQualifier(string|PRIQualifier $qualifier): self
    {
        $this->qualifier = $qualifier instanceof PRIQualifier ? $qualifier->value : $qualifier;
        return $this;
    }

    public function withPrice(int|float|string $price): self
    {
        $this->price = (string) $price;
        return $this;
    }

    public function withPriceType(string $priceType): self
    {
        $this->priceType = $priceType;
        return $this;
    }

    public function build(): PRIPrice
    {
        $rawValues = [
            'PRI',
            array_filter([$this->qualifier, $this->price, $this->priceType]),
        ];

        return new PRIPrice($rawValues);
    }
}
