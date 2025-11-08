<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Builder;

use EdifactParser\Segments\QTYQuantity;

/**
 * Fluent builder for QTY (Quantity) segments
 */
final class QTYBuilder
{
    private string $qualifier = '';
    private string $quantity = '';
    private string $measureUnit = '';

    public function withQualifier(string $qualifier): self
    {
        $this->qualifier = $qualifier;
        return $this;
    }

    public function withQuantity(int|float|string $quantity): self
    {
        $this->quantity = (string) $quantity;
        return $this;
    }

    public function withMeasureUnit(string $unit): self
    {
        $this->measureUnit = $unit;
        return $this;
    }

    public function build(): QTYQuantity
    {
        $rawValues = [
            'QTY',
            array_filter(
                [$this->qualifier, $this->quantity, $this->measureUnit],
                static fn ($value) => $value !== ''
            ),
        ];

        return new QTYQuantity($rawValues);
    }
}
