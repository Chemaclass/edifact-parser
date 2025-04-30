<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

// todo: make enum
class SectionControlIdentifier
{
    public const BetweenHeaderAndMessageDetails = 'D';
    public const BetweenMessageDetailsAndSummary = 'S';
}
