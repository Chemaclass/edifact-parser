<?php

declare(strict_types=1);

namespace AnalyticsBundle\tests\unit\Service\EDIParser\Segments;

use App\EdifactParser\Segments\UNTMessageFooter;
use PHPUnit\Framework\TestCase;

final class UNTMessageFooterTest extends TestCase
{
    /** @test */
    public function subSegmentKey(): void
    {
        $segment = new UNTMessageFooter(['UNT', '19', '1']);
        $this->assertEquals('19', $segment->subSegmentKey());
    }
}
