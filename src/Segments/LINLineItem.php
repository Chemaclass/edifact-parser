<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use function is_array;

/** @psalm-immutable */
final class LINLineItem extends AbstractSegment
{
    public function tag(): string
    {
        return 'LIN';
    }

    /**
     * Line item number
     */
    public function lineNumber(): string
    {
        return $this->rawValues()[1] ?? '';
    }

    /**
     * Item number identification (array format: [item number, item type code, code list qualifier])
     *
     * @return array<int, string>
     */
    public function itemNumberIdentification(): array
    {
        $value = $this->rawValues()[3] ?? [];
        return is_array($value) ? $value : [];
    }

    /**
     * Item number (product identifier)
     */
    public function itemNumber(): string
    {
        return $this->itemNumberIdentification()[0] ?? '';
    }

    /**
     * Item type code (e.g., 'EN' = EAN, 'SA' = Supplier article number)
     */
    public function itemTypeCode(): string
    {
        return $this->itemNumberIdentification()[1] ?? '';
    }
}
