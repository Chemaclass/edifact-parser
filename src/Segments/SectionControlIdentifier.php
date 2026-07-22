<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/**
 * UNS section-control identifiers (EDIFACT data element 0081) that separate the
 * header, detail and summary sections of a message.
 */
final class SectionControlIdentifier
{
    /** Boundary between the header and detail sections */
    public const BetweenHeaderAndMessageDetails = 'D';

    /** Boundary between the detail and summary sections */
    public const BetweenMessageDetailsAndSummary = 'S';

    private function __construct()
    {
    }
}
