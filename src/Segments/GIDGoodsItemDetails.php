<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class GIDGoodsItemDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'GID';
    }

    /**
     * Goods item number (sequence within the consignment)
     */
    public function goodsItemNumber(): string
    {
        return $this->element(1);
    }

    /**
     * Number of packages (first component of the number-and-type element)
     */
    public function numberOfPackages(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * Type of packages code, when present in the composite
     */
    public function packageTypeCode(): string
    {
        return $this->component(1, 2);
    }
}
