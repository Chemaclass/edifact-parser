<?php

declare(strict_types=1);

namespace App\Tests\EdifactParser\Segments;

use App\EdifactParser\Segments\CNTControl;
use PHPUnit\Framework\TestCase;

final class CNTControlTest extends TestCase
{
    /** @test */
    public function segmentValues(): void
    {
        $rawValues = ['CNT', ['7', '0.1', 'KGM']];
        $segment = new CNTControl($rawValues);

        self::assertEquals('CNT', $segment->name());
        self::assertEquals('7', $segment->subSegmentKey());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
