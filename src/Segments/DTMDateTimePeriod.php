<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use EdifactParser\Exception\MissingSubId;

/** @psalm-immutable */
final class DTMDateTimePeriod extends AbstractSegment
{
    public function tag(): string
    {
        return 'DTM';
    }

    public function subId(): string
    {
        if (!isset($this->rawValues[1][0])) {
            throw new MissingSubId('[1][0]', $this->rawValues);
        }

        return (string) $this->rawValues[1][0];
    }
}
