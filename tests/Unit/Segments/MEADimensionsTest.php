<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\MEADimensions;
use PHPUnit\Framework\TestCase;

final class MEADimensionsTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['MEA', 'WT', 'G', ['KGM', '0.1']];
        $segment = new MEADimensions($rawValues);

        self::assertEquals('MEA', $segment->tag());
        self::assertEquals('WT', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function typed_accessors(): void
    {
        $segment = new MEADimensions(['MEA', 'WT', 'G', ['KGM', '0.62']]);

        self::assertSame('WT', $segment->measurementPurpose());
        self::assertSame('G', $segment->measuredAttribute());
        self::assertSame('KGM', $segment->unitCode());
        self::assertSame('0.62', $segment->value());
    }

    /**
     * @test
     */
    public function measured_attribute_is_empty_when_omitted(): void
    {
        $segment = new MEADimensions(['MEA', 'VOL', '', ['MTQ', '0']]);

        self::assertSame('VOL', $segment->measurementPurpose());
        self::assertSame('', $segment->measuredAttribute());
        self::assertSame('MTQ', $segment->unitCode());
        self::assertSame('0', $segment->value());
    }
}
