<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\SectionControlIdentifier;
use PHPUnit\Framework\TestCase;

class SectionControlIdentifierTest extends TestCase
{
    /**
     * @test
     */
    public function indicates_end_of_details_section(): void
    {
        self::assertFalse(SectionControlIdentifier::BetweenHeaderAndMessageDetails->indicatesEndOfDetailsSection());
        self::assertTrue(SectionControlIdentifier::BetweenMessageDetailsAndSummary->indicatesEndOfDetailsSection());
    }
}
