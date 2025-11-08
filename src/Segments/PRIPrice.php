<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use EdifactParser\Segments\Builder\PRIBuilder;

use function is_array;

/** @psalm-immutable */
final class PRIPrice extends AbstractSegment
{
    public function tag(): string
    {
        return 'PRI';
    }

    /**
     * Create a new builder instance
     */
    public static function builder(): PRIBuilder
    {
        return new PRIBuilder();
    }

    public function subId(): string
    {
        return $this->rawValues[1][0];
    }

    /**
     * Price qualifier (e.g., 'AAA' = Calculation net, 'AAB' = Gross)
     */
    public function qualifier(): string
    {
        $value = $this->rawValues()[1] ?? [];
        return is_array($value) ? ($value[0] ?? '') : '';
    }

    /**
     * Price amount
     */
    public function price(): string
    {
        $value = $this->rawValues()[1] ?? [];
        return is_array($value) ? ($value[1] ?? '') : '';
    }

    /**
     * Price as float
     */
    public function priceAsFloat(): float
    {
        return (float) $this->price();
    }

    /**
     * Price type qualifier
     */
    public function priceType(): string
    {
        $value = $this->rawValues()[1] ?? [];
        return is_array($value) ? ($value[2] ?? '') : '';
    }
}
