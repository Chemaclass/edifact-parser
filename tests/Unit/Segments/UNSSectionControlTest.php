<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\UNSSectionControl;
use PHPUnit\Framework\TestCase;

class UNSSectionControlTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['UNS', 'D'];
        $segment = new UNSSectionControl($rawValues);

        self::assertEquals('UNS', $segment->tag());
        self::assertEquals('D', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function indicates_end_of_details_section(): void
    {
        self::assertFalse((new UNSSectionControl(['UNS', 'D']))->indicatesEndOfDetailsSection());
        self::assertTrue((new UNSSectionControl(['UNS', 'S']))->indicatesEndOfDetailsSection());
    }
}
