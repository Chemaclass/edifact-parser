<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments\Builder;

use EdifactParser\Segments\Builder\NADBuilder;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\Qualifier\NADQualifier;
use PHPUnit\Framework\TestCase;

final class NADBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function builds_complete_segment_with_all_fields(): void
    {
        $segment = NADNameAddress::builder()
            ->withQualifier('BY')
            ->withPartyId('123456')
            ->withName('ACME Corporation')
            ->withStreet('123 Main Street')
            ->withCity('Springfield')
            ->withPostalCode('12345')
            ->withCountryCode('US')
            ->build();

        self::assertEquals('NAD', $segment->tag());
        self::assertEquals('BY', $segment->partyQualifier());
        self::assertEquals('123456', $segment->partyId());
        self::assertEquals('ACME Corporation', $segment->name());
        self::assertEquals('123 Main Street', $segment->street());
        self::assertEquals('Springfield', $segment->city());
        self::assertEquals('12345', $segment->postalCode());
        self::assertEquals('US', $segment->countryCode());
    }

    /**
     * @test
     */
    public function builds_segment_with_const_qualifier(): void
    {
        $segment = NADNameAddress::builder()
            ->withQualifier(NADQualifier::SUPPLIER)
            ->withName('Supplier Inc')
            ->build();

        self::assertEquals('SU', $segment->partyQualifier());
        self::assertEquals('Supplier Inc', $segment->name());
    }

    /**
     * @test
     */
    public function builds_minimal_segment(): void
    {
        $segment = NADNameAddress::builder()
            ->withQualifier('CN')
            ->build();

        self::assertEquals('NAD', $segment->tag());
        self::assertEquals('CN', $segment->partyQualifier());
    }

    /**
     * @test
     */
    public function fluent_interface_returns_builder(): void
    {
        $builder = new NADBuilder();

        self::assertSame($builder, $builder->withQualifier('BY'));
        self::assertSame($builder, $builder->withName('Test'));
        self::assertSame($builder, $builder->withStreet('Street'));
        self::assertSame($builder, $builder->withCity('City'));
        self::assertSame($builder, $builder->withPostalCode('12345'));
        self::assertSame($builder, $builder->withCountryCode('US'));
    }

    /**
     * @test
     */
    public function builds_segment_with_party_identification(): void
    {
        $segment = NADNameAddress::builder()
            ->withQualifier(NADQualifier::BUYER)
            ->withPartyId('987654', '160', 'Z12')
            ->build();

        self::assertEquals('BY', $segment->partyQualifier());
        self::assertEquals(['987654', '160', 'Z12'], $segment->partyIdentification());
        self::assertEquals('987654', $segment->partyId());
    }
}
