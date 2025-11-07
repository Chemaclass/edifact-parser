<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use function is_array;

/** @psalm-immutable */
final class QTYQuantity extends AbstractSegment
{
    public function tag(): string
    {
        return 'QTY';
    }

    public function subId(): string
    {
        return $this->rawValues[1][0];
    }

    /**
     * Quantity qualifier (e.g., '21' = Ordered quantity, '12' = Dispatched quantity)
     */
    public function qualifier(): string
    {
        $value = $this->rawValues()[1] ?? [];
        return is_array($value) ? ($value[0] ?? '') : '';
    }

    /**
     * Quantity value
     */
    public function quantity(): string
    {
        $value = $this->rawValues()[1] ?? [];
        return is_array($value) ? ($value[1] ?? '') : '';
    }

    /**
     * Quantity as float
     */
    public function quantityAsFloat(): float
    {
        return (float) $this->quantity();
    }

    /**
     * Measure unit qualifier (e.g., 'PCE' = Piece, 'KGM' = Kilogram)
     */
    public function measureUnit(): string
    {
        $value = $this->rawValues()[1] ?? [];
        return is_array($value) ? ($value[2] ?? '') : '';
    }
}
