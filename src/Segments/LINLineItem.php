<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class LINLineItem extends AbstractSegment
{
    public function tag(): string
    {
        return 'LIN';
    }

    public function subId(): string
    {
        return $this->rawValues[1];
    }
}
