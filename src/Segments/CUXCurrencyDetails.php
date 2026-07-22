<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class CUXCurrencyDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'CUX';
    }

    public function subId(): string
    {
        return $this->requiredSubId();
    }
}
