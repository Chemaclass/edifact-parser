<?php

declare(strict_types=1);

namespace AnalyticsBundle\tests\unit\Service\EDIParser\Segments;

use App\EdifactParser\Segments\UNHMessageHeader;
use PHPUnit\Framework\TestCase;

final class UNHMessageHeaderTest extends TestCase
{
    /** @test */
    public function subSegmentKey(): void
    {
        $segment = new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]);
        $this->assertEquals('1', $segment->subSegmentKey());
    }
}
