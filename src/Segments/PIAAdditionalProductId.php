<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class PIAAdditionalProductId extends AbstractSegment
{
    public function tag(): string
    {
        return 'PIA';
    }

    /**
     * Product id function qualifier (4347), e.g. '1' = additional identification,
     * '5' = product identification.
     */
    public function productIdFunctionQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * Item number (C212/7140): PIA+5+ABC123:SA -> 'ABC123'.
     */
    public function itemNumber(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * Item number type code (C212/7143): PIA+5+ABC123:SA -> 'SA' (EAN, buyer's
     * article number, etc.).
     */
    public function itemTypeCode(): string
    {
        return $this->component(1, 2);
    }
}
