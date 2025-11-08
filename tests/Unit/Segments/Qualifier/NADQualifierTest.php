<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments\Qualifier;

use EdifactParser\Segments\Qualifier\NADQualifier;
use Error;
use PHPUnit\Framework\TestCase;

final class NADQualifierTest extends TestCase
{
    /**
     * @test
     */
    public function const_values_are_correct(): void
    {
        self::assertEquals('BY', NADQualifier::BUYER);
        self::assertEquals('SU', NADQualifier::SUPPLIER);
        self::assertEquals('CN', NADQualifier::CONSIGNEE);
        self::assertEquals('CZ', NADQualifier::CONSIGNOR);
        self::assertEquals('DP', NADQualifier::DELIVERY_PARTY);
        self::assertEquals('IV', NADQualifier::INVOICEE);
        self::assertEquals('PR', NADQualifier::PAYER);
        self::assertEquals('CA', NADQualifier::CARRIER);
        self::assertEquals('FW', NADQualifier::FREIGHT_FORWARDER);
        self::assertEquals('MF', NADQualifier::MANUFACTURER);
        self::assertEquals('UC', NADQualifier::ULTIMATE_CONSIGNEE);
        self::assertEquals('WH', NADQualifier::WAREHOUSE_KEEPER);
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
     *
     * @psalm-suppress InaccessibleMethod
     */
    public function cannot_be_instantiated(): void
    {
        $this->expectException(Error::class);

        new NADQualifier();
    }
}
