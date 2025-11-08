<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments\Qualifier;

use EdifactParser\Segments\Qualifier\PRIQualifier;
use PHPUnit\Framework\TestCase;

final class PRIQualifierTest extends TestCase
{
    /**
     * @test
     */
    public function enum_values_are_correct(): void
    {
        self::assertEquals('AAA', PRIQualifier::CALCULATION_NET->value);
        self::assertEquals('AAB', PRIQualifier::CALCULATION_GROSS->value);
        self::assertEquals('AAE', PRIQualifier::INFORMATION_PRICE->value);
        self::assertEquals('AAF', PRIQualifier::GROSS->value);
        self::assertEquals('AAG', PRIQualifier::NET->value);
        self::assertEquals('CAL', PRIQualifier::CATALOGUE->value);
        self::assertEquals('CT', PRIQualifier::CONTRACT->value);
        self::assertEquals('DIS', PRIQualifier::DISCOUNT->value);
        self::assertEquals('LIS', PRIQualifier::LIST->value);
        self::assertEquals('MIN', PRIQualifier::MINIMUM_ORDER->value);
        self::assertEquals('RRP', PRIQualifier::RECOMMENDED_RETAIL->value);
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
     */
    public function can_get_all_cases(): void
    {
        $cases = PRIQualifier::cases();

        self::assertCount(11, $cases);
        self::assertContains(PRIQualifier::CALCULATION_NET, $cases);
        self::assertContains(PRIQualifier::GROSS, $cases);
    }

    /**
     * @test
     */
    public function can_create_from_string(): void
    {
        $qualifier = PRIQualifier::from('AAA');

        self::assertSame(PRIQualifier::CALCULATION_NET, $qualifier);
    }

    /**
     * @test
     *
     * @psalm-suppress RedundantCondition
     */
    public function try_from_returns_null_for_invalid_value(): void
    {
        $qualifier = PRIQualifier::tryFrom('INVALID');

        /** @psalm-suppress RedundantCondition */
        self::assertNull($qualifier);
    }
}
