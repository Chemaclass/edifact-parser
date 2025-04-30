<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class BGMBeginningOfMessage extends AbstractSegment
{
    public function tag(): string
    {
        return 'BGM';
    }
}
