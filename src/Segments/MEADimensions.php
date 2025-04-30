<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class MEADimensions extends AbstractSegment
{
    public function tag(): string
    {
        return 'MEA';
    }

    public function subId(): string
    {
        return (string) $this->rawValues[1];
    }
}
