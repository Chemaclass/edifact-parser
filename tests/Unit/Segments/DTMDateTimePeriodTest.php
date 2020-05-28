<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\DTMDateTimePeriod;
use PHPUnit\Framework\TestCase;

final class DTMDateTimePeriodTest extends TestCase
{
    /** @test */
    public function segmentValues(): void
    {
        $rawValues = ['DTM', ['10', '20191002', '102']];
        $segment = new DTMDateTimePeriod($rawValues);

        self::assertEquals(DTMDateTimePeriod::class, $segment->name());
        self::assertEquals('10', $segment->subSegmentKey());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
