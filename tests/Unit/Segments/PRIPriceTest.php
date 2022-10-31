<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\PRIPrice;
use PHPUnit\Framework\TestCase;

class PRIPriceTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['PRI', ['AAA', '225']];
        $segment = new PRIPrice($rawValues);

        self::assertEquals('PRI', $segment->tag());
        self::assertEquals('AAA', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
