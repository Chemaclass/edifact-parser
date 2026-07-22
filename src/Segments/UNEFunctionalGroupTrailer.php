<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class UNEFunctionalGroupTrailer extends AbstractSegment
{
    public function tag(): string
    {
        return 'UNE';
    }

    /**
     * Number of messages in the functional group
     */
    public function controlCount(): string
    {
        return $this->element(1);
    }

    /**
     * Functional group reference number (must match the UNG)
     */
    public function groupReference(): string
    {
        return $this->element(2);
    }
}
