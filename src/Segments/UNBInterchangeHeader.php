<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use EdifactParser\Exception\MissingSubId;

/** @psalm-immutable */
final class UNBInterchangeHeader extends AbstractSegment
{
    public function tag(): string
    {
        return 'UNB';
    }

    public function subId(): string
    {
        if (!isset($this->rawValues[1][0])) {
            throw new MissingSubId('[1][0]', $this->rawValues);
        }

        return (string) $this->rawValues[1][0];
    }
}
