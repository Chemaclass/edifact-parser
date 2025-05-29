<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class MEADimensions extends AbstractSegment
{
    public function tag(): string
    {
        return 'MEA';
    }
}
