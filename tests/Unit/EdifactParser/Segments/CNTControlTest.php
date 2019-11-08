<?php

declare(strict_types=1);

namespace AnalyticsBundle\tests\unit\Service\EDIParser\Segments;

use App\EdifactParser\Segments\CNTControl;
use PHPUnit\Framework\TestCase;

final class CNTControlTest extends TestCase
{
    /** @test */
    public function subSegmentKey(): void
    {
        $segment = new CNTControl(['CNT', ['7', '0.1', 'KGM']]);
        $this->assertEquals('7', $segment->subSegmentKey());
    }
}
