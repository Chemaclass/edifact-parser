<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class RFFReference extends AbstractSegment
{
    public function tag(): string
    {
        return 'RFF';
    }

    public function subId(): string
    {
        return $this->rawValues[1][0];
    }
}
