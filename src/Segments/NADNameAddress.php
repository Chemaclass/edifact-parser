<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class NADNameAddress extends AbstractSegment
{
    public function tag(): string
    {
        return 'NAD';
    }

    public function subId(): string
    {
        return (string) $this->rawValues[1];
    }
}
