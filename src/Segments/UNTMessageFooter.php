<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class UNTMessageFooter extends AbstractSegment
{
    public function tag(): string
    {
        return 'UNT';
    }

    /**
     * Number of segments in the message (including UNH and UNT)
     */
    public function segmentCount(): string
    {
        return $this->element(1);
    }

    /**
     * Message reference number (must match the UNH)
     */
    public function messageReferenceNumber(): string
    {
        return $this->element(2);
    }
}
