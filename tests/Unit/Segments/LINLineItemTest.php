<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\LINLineItem;
use PHPUnit\Framework\TestCase;

class LINLineItemTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['LIN', '2'];
        $segment = new LINLineItem($rawValues);

        self::assertEquals('LIN', $segment->tag());
        self::assertEquals('2', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }
}
