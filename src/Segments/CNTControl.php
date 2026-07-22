<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class CNTControl extends AbstractSegment
{
    public function tag(): string
    {
        return 'CNT';
    }

    public function subId(): string
    {
        return $this->requiredSubId();
    }
}
