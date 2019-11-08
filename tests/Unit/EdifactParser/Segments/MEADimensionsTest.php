<?php

declare(strict_types=1);

namespace AnalyticsBundle\tests\unit\Service\EDIParser\Segments;

use App\EdifactParser\Segments\MEADimensions;
use PHPUnit\Framework\TestCase;

final class MEADimensionsTest extends TestCase
{
    /** @test */
    public function subSegmentKey(): void
    {
        $segment = new MEADimensions(['MEA', 'WT', 'G', ['KGM', '0.1']]);
        $this->assertEquals('WT', $segment->subSegmentKey());
    }
}
