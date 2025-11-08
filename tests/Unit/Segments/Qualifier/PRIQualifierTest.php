<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments\Qualifier;

use EdifactParser\Segments\Qualifier\PRIQualifier;
use Error;
use PHPUnit\Framework\TestCase;

final class PRIQualifierTest extends TestCase
{
    /**
     * @test
     */
    public function const_values_are_correct(): void
    {
        self::assertEquals('AAA', PRIQualifier::CALCULATION_NET);
        self::assertEquals('AAB', PRIQualifier::CALCULATION_GROSS);
        self::assertEquals('AAE', PRIQualifier::INFORMATION_PRICE);
        self::assertEquals('AAF', PRIQualifier::GROSS);
        self::assertEquals('AAG', PRIQualifier::NET);
        self::assertEquals('CAL', PRIQualifier::CATALOGUE);
        self::assertEquals('CT', PRIQualifier::CONTRACT);
        self::assertEquals('DIS', PRIQualifier::DISCOUNT);
        self::assertEquals('LIS', PRIQualifier::LIST);
        self::assertEquals('MIN', PRIQualifier::MINIMUM_ORDER);
        self::assertEquals('RRP', PRIQualifier::RECOMMENDED_RETAIL);
    }

    /**
     * @test
     */
    public function can_be_used_in_match_expressions(): void
    {
        $testCases = [
            PRIQualifier::CALCULATION_NET,
            PRIQualifier::GROSS,
            PRIQualifier::LIST,
        ];

        $expected = [
            'net',
            'gross',
            'list',
        ];

        foreach ($testCases as $index => $qualifier) {
            $result = match ($qualifier) {
                PRIQualifier::CALCULATION_NET => 'net',
                PRIQualifier::GROSS => 'gross',
                PRIQualifier::LIST => 'list',
                default => 'other',
            };

            self::assertEquals($expected[$index], $result);
        }
    }

    /**
     * @test
     *
     * @psalm-suppress InaccessibleMethod
     */
    public function cannot_be_instantiated(): void
    {
        $this->expectException(Error::class);

        new PRIQualifier();
    }
}
