<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Exception\MissingSubId;
use EdifactParser\Segments\CNTControl;
use PHPUnit\Framework\TestCase;

final class CNTControlTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['CNT', ['7', '0.1', 'KGM']];
        $segment = new CNTControl($rawValues);

        self::assertEquals('CNT', $segment->tag());
        self::assertEquals('7', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function typed_accessors(): void
    {
        $segment = new CNTControl(['CNT', ['7', '0.62', 'KGM']]);

        self::assertSame('7', $segment->controlQualifier());
        self::assertSame('0.62', $segment->controlValue());
        self::assertSame('KGM', $segment->measureUnit());
    }

    /**
     * @test
     */
    public function measure_unit_is_empty_when_absent(): void
    {
        $segment = new CNTControl(['CNT', ['2', '2']]);

        self::assertSame('2', $segment->controlQualifier());
        self::assertSame('2', $segment->controlValue());
        self::assertSame('', $segment->measureUnit());
    }

    /**
     * @test
     */
    public function missing_sub_id(): void
    {
        $segment = new CNTControl(['CNT']);
        $this->expectException(MissingSubId::class);
        $segment->subId();
    }
}
