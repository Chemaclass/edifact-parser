<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

enum SectionControlIdentifier: string
{
    case BetweenHeaderAndMessageDetails = 'D';
    case BetweenMessageDetailsAndSummary = 'S';

    public function indicatesEndOfDetailsSection(): bool
    {
        return $this == SectionControlIdentifier::BetweenMessageDetailsAndSummary;
    }
}
