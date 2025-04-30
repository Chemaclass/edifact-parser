<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class PCIPackageId extends AbstractSegment
{
    public function tag(): string
    {
        return 'PCI';
    }
}
