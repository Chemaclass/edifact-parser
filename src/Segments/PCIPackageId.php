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

    /**
     * Marking instructions code (4233): PCI+18+05055700896 -> '18'.
     */
    public function markingInstructionsCode(): string
    {
        return $this->element(1);
    }

    /**
     * Shipping marks / first marks-and-labels value (C210/7102):
     * PCI+18+05055700896 -> '05055700896'.
     */
    public function marksAndLabels(): string
    {
        return $this->firstComponent(2);
    }
}
