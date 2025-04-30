<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
class CUXCurrencyDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'CUX';
    }

    public function subId(): string
    {
        return $this->rawValues[1][0];
    }
}
