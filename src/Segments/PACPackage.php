<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class PACPackage extends AbstractSegment
{
    public function tag(): string
    {
        return 'PAC';
    }

    public function numberOfPackages(): string
    {
        return $this->element(1);
    }

    /**
     * Packaging type code (e.g., 'CT' = Carton, 'PX' = Pallet, 'BX' = Box)
     */
    public function packagingTypeCode(): string
    {
        return $this->firstComponent(3);
    }
}
