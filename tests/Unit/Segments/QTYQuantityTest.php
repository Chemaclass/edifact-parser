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
}
