<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class UNBInterchangeHeader extends AbstractSegment
{
    public function tag(): string
    {
        return 'UNB';
    }

    public function subId(): string
    {
        return $this->requiredSubId();
    }
}
