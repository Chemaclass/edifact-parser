<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class UNGFunctionalGroupHeader extends AbstractSegment
{
    public function tag(): string
    {
        return 'UNG';
    }

    /**
     * Message type grouped by this functional group (e.g., 'ORDERS', 'INVOIC')
     */
    public function messageType(): string
    {
        return $this->element(1);
    }

    /**
     * Functional group reference number
     */
    public function groupReference(): string
    {
        return $this->element(5);
    }
}
