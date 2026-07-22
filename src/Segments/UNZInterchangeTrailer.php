<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class UNZInterchangeTrailer extends AbstractSegment
{
    public function tag(): string
    {
        return 'UNZ';
    }

    /**
     * Interchange control count: number of messages (or functional groups) in the interchange
     */
    public function interchangeControlCount(): string
    {
        return $this->element(1);
    }

    /**
     * Interchange control reference (must match the UNB)
     */
    public function interchangeControlReference(): string
    {
        return $this->element(2);
    }
}
