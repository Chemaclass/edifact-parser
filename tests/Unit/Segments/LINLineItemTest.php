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

    /**
     * @test
     */
    public function typed_accessors(): void
    {
        $segment = new LINLineItem(['LIN', '1', '', ['5449000000996', 'EN']]);

        self::assertSame('1', $segment->lineNumber());
        self::assertSame(['5449000000996', 'EN'], $segment->itemNumberIdentification());
        self::assertSame('5449000000996', $segment->itemNumber());
        self::assertSame('EN', $segment->itemTypeCode());
    }

    /**
     * @test
     */
    public function item_number_identification_is_empty_when_absent(): void
    {
        self::assertSame([], (new LINLineItem(['LIN', '1']))->itemNumberIdentification());
    }
}
