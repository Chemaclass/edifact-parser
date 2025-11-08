<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments\Builder;

use EdifactParser\Segments\Builder\PRIBuilder;
use EdifactParser\Segments\PRIPrice;
use EdifactParser\Segments\Qualifier\PRIQualifier;
use PHPUnit\Framework\TestCase;

final class PRIBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function builds_segment_with_all_fields(): void
    {
        $segment = PRIPrice::builder()
            ->withQualifier('AAA')
            ->withPrice(99.99)
            ->withPriceType('CT')
            ->build();

        self::assertEquals('PRI', $segment->tag());
        self::assertEquals('AAA', $segment->qualifier());
        self::assertEquals('99.99', $segment->price());
        self::assertEquals(99.99, $segment->priceAsFloat());
        self::assertEquals('CT', $segment->priceType());
    }

    /**
     * @test
     */
    public function builds_segment_with_enum_qualifier(): void
    {
        $segment = PRIPrice::builder()
            ->withQualifier(PRIQualifier::CALCULATION_NET)
            ->withPrice(150.50)
            ->build();

        self::assertEquals('AAA', $segment->qualifier());
        self::assertEquals('150.5', $segment->price());
        self::assertEquals(150.5, $segment->priceAsFloat());
    }

    /**
     * @test
     */
    public function builds_segment_with_integer_price(): void
    {
        $segment = PRIPrice::builder()
            ->withQualifier(PRIQualifier::GROSS)
            ->withPrice(100)
            ->build();

        self::assertEquals('AAF', $segment->qualifier());
        self::assertEquals('100', $segment->price());
        self::assertEquals(100.0, $segment->priceAsFloat());
    }

    /**
     * @test
     */
    public function builds_segment_with_string_price(): void
    {
        $segment = PRIPrice::builder()
            ->withQualifier('AAA')
            ->withPrice('1234.56')
            ->build();

        self::assertEquals('AAA', $segment->qualifier());
        self::assertEquals('1234.56', $segment->price());
        self::assertEquals(1234.56, $segment->priceAsFloat());
    }

    /**
     * @test
     */
    public function fluent_interface_returns_builder(): void
    {
        $builder = new PRIBuilder();

        self::assertSame($builder, $builder->withQualifier('AAA'));
        self::assertSame($builder, $builder->withPrice(99.99));
        self::assertSame($builder, $builder->withPriceType('CT'));
    }

    /**
     * @test
     */
    public function builds_minimal_segment(): void
    {
        $segment = PRIPrice::builder()
            ->withQualifier('AAA')
            ->withPrice(50)
            ->build();

        self::assertEquals('PRI', $segment->tag());
        self::assertEquals('AAA', $segment->qualifier());
        self::assertEquals('50', $segment->price());
    }
}
