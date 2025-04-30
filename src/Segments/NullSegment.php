<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class NullSegment extends AbstractSegment
{
    public function tag(): string
    {
        return '';
    }
}
