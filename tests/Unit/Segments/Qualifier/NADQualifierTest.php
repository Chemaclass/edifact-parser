<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments\Qualifier;

use EdifactParser\Segments\Qualifier\NADQualifier;
use PHPUnit\Framework\TestCase;

final class NADQualifierTest extends TestCase
{
    /**
     * @test
     */
    public function enum_values_are_correct(): void
    {
        self::assertEquals('BY', NADQualifier::BUYER->value);
        self::assertEquals('SU', NADQualifier::SUPPLIER->value);
        self::assertEquals('CN', NADQualifier::CONSIGNEE->value);
        self::assertEquals('CZ', NADQualifier::CONSIGNOR->value);
        self::assertEquals('DP', NADQualifier::DELIVERY_PARTY->value);
        self::assertEquals('IV', NADQualifier::INVOICEE->value);
        self::assertEquals('PR', NADQualifier::PAYER->value);
        self::assertEquals('CA', NADQualifier::CARRIER->value);
        self::assertEquals('FW', NADQualifier::FREIGHT_FORWARDER->value);
        self::assertEquals('MF', NADQualifier::MANUFACTURER->value);
        self::assertEquals('UC', NADQualifier::ULTIMATE_CONSIGNEE->value);
        self::assertEquals('WH', NADQualifier::WAREHOUSE_KEEPER->value);
    }

    /**
     * @test
     */
    public function can_be_used_in_match_expressions(): void
    {
        $testCases = [
            NADQualifier::BUYER,
            NADQualifier::SUPPLIER,
            NADQualifier::CONSIGNEE,
        ];

        $expected = [
            'buyer',
            'supplier',
            'consignee',
        ];

        foreach ($testCases as $index => $qualifier) {
            $result = match ($qualifier) {
                NADQualifier::BUYER => 'buyer',
                NADQualifier::SUPPLIER => 'supplier',
                NADQualifier::CONSIGNEE => 'consignee',
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
        $cases = NADQualifier::cases();

        self::assertCount(12, $cases);
        self::assertContains(NADQualifier::BUYER, $cases);
        self::assertContains(NADQualifier::SUPPLIER, $cases);
    }

    /**
     * @test
     */
    public function can_create_from_string(): void
    {
        $qualifier = NADQualifier::from('BY');

        self::assertSame(NADQualifier::BUYER, $qualifier);
    }

    /**
     * @test
     *
     * @psalm-suppress RedundantCondition
     */
    public function try_from_returns_null_for_invalid_value(): void
    {
        $qualifier = NADQualifier::tryFrom('INVALID');

        /** @psalm-suppress RedundantCondition */
        self::assertNull($qualifier);
    }
}
