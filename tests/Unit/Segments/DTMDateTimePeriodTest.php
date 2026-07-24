<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use DateTimeImmutable;
use EdifactParser\Exception\MissingSubId;
use EdifactParser\Segments\DTMDateTimePeriod;
use PHPUnit\Framework\TestCase;

final class DTMDateTimePeriodTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['DTM', ['10', '20191002', '102']];
        $segment = new DTMDateTimePeriod($rawValues);

        self::assertEquals('DTM', $segment->tag());
        self::assertEquals('10', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function typed_accessors(): void
    {
        $segment = new DTMDateTimePeriod(['DTM', ['10', '20191002', '102']]);

        self::assertSame('10', $segment->qualifier());
        self::assertSame('20191002', $segment->dateTime());
        self::assertSame('102', $segment->formatQualifier());
    }

    /**
     * @test
     *
     * @dataProvider parseableDateTimes
     */
    public function as_date_time_parses_supported_formats(string $value, string $format, string $displayFormat, string $expected): void
    {
        $segment = new DTMDateTimePeriod(['DTM', ['10', $value, $format]]);

        $parsed = $segment->asDateTime();
        self::assertInstanceOf(DateTimeImmutable::class, $parsed);
        // Assert only the precision the EDIFACT format actually carries.
        self::assertSame($expected, $parsed->format($displayFormat));
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: string}>
     */
    public static function parseableDateTimes(): array
    {
        return [
            'CCYYMMDD (102)' => ['20191002', '102', 'Y-m-d', '2019-10-02'],
            'CCYYMMDDHHMM (203)' => ['201910021530', '203', 'Y-m-d H:i', '2019-10-02 15:30'],
            'CCYYMMDDHHMMSS (204)' => ['20191002153045', '204', 'Y-m-d H:i:s', '2019-10-02 15:30:45'],
        ];
    }

    /**
     * @test
     */
    public function as_date_time_returns_null_for_empty_value(): void
    {
        self::assertNull((new DTMDateTimePeriod(['DTM', ['10', '', '102']]))->asDateTime());
    }

    /**
     * @test
     */
    public function as_date_time_returns_null_for_unsupported_format(): void
    {
        self::assertNull((new DTMDateTimePeriod(['DTM', ['10', '20191002', '999']]))->asDateTime());
    }

    /**
     * @test
     */
    public function as_date_time_returns_null_when_value_does_not_match_format(): void
    {
        self::assertNull((new DTMDateTimePeriod(['DTM', ['10', 'not-a-date', '102']]))->asDateTime());
    }

    /**
     * @test
     */
    public function missing_sub_id(): void
    {
        $segment = new DTMDateTimePeriod(['DTM']);
        $this->expectException(MissingSubId::class);
        $segment->subId();
    }
}
