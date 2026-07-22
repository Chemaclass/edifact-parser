<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use EdifactParser\Segments\Builder\QTYBuilder;

/** @psalm-immutable */
final class QTYQuantity extends AbstractSegment
{
    public function tag(): string
    {
        return 'QTY';
    }

    public static function builder(): QTYBuilder
    {
        return new QTYBuilder();
    }

    public function subId(): string
    {
        return $this->requiredSubId();
    }

    /**
     * Quantity qualifier (e.g., '21' = Ordered quantity, '12' = Dispatched quantity)
     */
    public function qualifier(): string
    {
        return $this->component(0);
    }

    public function quantity(): string
    {
        return $this->component(1);
    }

    public function quantityAsFloat(): float
    {
        return (float) $this->quantity();
    }

    /**
     * Measure unit qualifier (e.g., 'PCE' = Piece, 'KGM' = Kilogram)
     */
    public function measureUnit(): string
    {
        return $this->component(2);
    }
}
