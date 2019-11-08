<?php

declare(strict_types=1);

namespace AnalyticsBundle\tests\unit\Service\EDIParser\Segments;

use App\EdifactParser\Segments\DTMDateTimePeriod;
use PHPUnit\Framework\TestCase;

final class DTMDateTimePeriodTest extends TestCase
{
    /** @test */
    public function subSegmentKey(): void
    {
        $segment = new DTMDateTimePeriod(['DTM', ['10', '20191002', '102']]);
        $this->assertEquals('10', $segment->subSegmentKey());
    }
}
