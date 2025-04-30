<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class PRIPrice extends AbstractSegment
{
    public function tag(): string
    {
        return 'PRI';
    }

    public function subId(): string
    {
        return $this->rawValues[1][0];
    }
}
