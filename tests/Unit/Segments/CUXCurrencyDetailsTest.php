<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\CUXCurrencyDetails;
use PHPUnit\Framework\TestCase;

class CUXCurrencyDetailsTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['CUX', ['5', 'GBP', '9']];
        $segment = new CUXCurrencyDetails($rawValues);

        self::assertEquals('CUX', $segment->tag());
        self::assertEquals('5', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function typed_accessors(): void
    {
        $segment = new CUXCurrencyDetails(['CUX', ['2', 'EUR', '4']]);

        self::assertSame('2', $segment->usageQualifier());
        self::assertSame('EUR', $segment->currencyCode());
        self::assertSame('4', $segment->rateQualifier());
    }
}
