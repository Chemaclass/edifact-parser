<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class UNSSectionControl extends AbstractSegment
{
    public function tag(): string
    {
        return 'UNS';
    }

    public function subId(): string
    {
        return $this->rawValues[1];
    }

    public function indicatesEndOfDetailsSection(): bool
    {
        return $this->rawValues[1] === SectionControlIdentifier::BetweenMessageDetailsAndSummary;
    }
}
