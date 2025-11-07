<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\QTYQuantity;
use PHPUnit\Framework\TestCase;

class QTYQuantityTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['QTY', ['21', '1']];
        $segment = new QTYQuantity($rawValues);

        self::assertEquals('QTY', $segment->tag());
        self::assertEquals('21', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function typed_accessors(): void
    {
        $rawValues = ['QTY', ['21', '100', 'PCE']];
        $segment = new QTYQuantity($rawValues);

        self::assertEquals('21', $segment->qualifier());
        self::assertEquals('100', $segment->quantity());
        self::assertEquals(100.0, $segment->quantityAsFloat());
        self::assertEquals('PCE', $segment->measureUnit());
    }

    /**
     * @test
     */
    public function quantity_as_float_with_decimal(): void
    {
        $rawValues = ['QTY', ['21', '12.5', 'KGM']];
        $segment = new QTYQuantity($rawValues);

        self::assertEquals(12.5, $segment->quantityAsFloat());
    }
}
