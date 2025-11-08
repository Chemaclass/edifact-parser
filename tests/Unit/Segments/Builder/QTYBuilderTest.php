<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments\Builder;

use EdifactParser\Segments\Builder\QTYBuilder;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Segments\Qualifier\QTYQualifier;
use PHPUnit\Framework\TestCase;

final class QTYBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function builds_segment_with_all_fields(): void
    {
        $segment = QTYQuantity::builder()
            ->withQualifier('21')
            ->withQuantity(100)
            ->withMeasureUnit('PCE')
            ->build();

        self::assertEquals('QTY', $segment->tag());
        self::assertEquals('21', $segment->qualifier());
        self::assertEquals('100', $segment->quantity());
        self::assertEquals(100.0, $segment->quantityAsFloat());
        self::assertEquals('PCE', $segment->measureUnit());
    }

    /**
     * @test
     */
    public function builds_segment_with_const_qualifier(): void
    {
        $segment = QTYQuantity::builder()
            ->withQualifier(QTYQualifier::ORDERED)
            ->withQuantity(50.5)
            ->build();

        self::assertEquals('21', $segment->qualifier());
        self::assertEquals('50.5', $segment->quantity());
        self::assertEquals(50.5, $segment->quantityAsFloat());
    }

    /**
     * @test
     */
    public function builds_segment_with_integer_quantity(): void
    {
        $segment = QTYQuantity::builder()
            ->withQualifier(QTYQualifier::DISPATCHED)
            ->withQuantity(25)
            ->build();

        self::assertEquals('12', $segment->qualifier());
        self::assertEquals('25', $segment->quantity());
        self::assertEquals(25.0, $segment->quantityAsFloat());
    }

    /**
     * @test
     */
    public function builds_segment_with_string_quantity(): void
    {
        $segment = QTYQuantity::builder()
            ->withQualifier('1')
            ->withQuantity('123.456')
            ->build();

        self::assertEquals('1', $segment->qualifier());
        self::assertEquals('123.456', $segment->quantity());
        self::assertEquals(123.456, $segment->quantityAsFloat());
    }

    /**
     * @test
     */
    public function fluent_interface_returns_builder(): void
    {
        $builder = new QTYBuilder();

        self::assertSame($builder, $builder->withQualifier('21'));
        self::assertSame($builder, $builder->withQuantity(100));
        self::assertSame($builder, $builder->withMeasureUnit('PCE'));
    }

    /**
     * @test
     */
    public function builds_minimal_segment(): void
    {
        $segment = QTYQuantity::builder()
            ->withQualifier('21')
            ->withQuantity(10)
            ->build();

        self::assertEquals('QTY', $segment->tag());
        self::assertEquals('21', $segment->qualifier());
        self::assertEquals('10', $segment->quantity());
    }

    /**
     * @test
     */
    public function preserves_zero_quantity_as_integer(): void
    {
        $segment = QTYQuantity::builder()
            ->withQualifier(QTYQualifier::ORDERED)
            ->withQuantity(0)
            ->withMeasureUnit('PCE')
            ->build();

        self::assertEquals('21', $segment->qualifier());
        self::assertEquals('0', $segment->quantity());
        self::assertEquals(0.0, $segment->quantityAsFloat());
        self::assertEquals('PCE', $segment->measureUnit());
    }

    /**
     * @test
     */
    public function preserves_zero_quantity_as_float(): void
    {
        $segment = QTYQuantity::builder()
            ->withQualifier(QTYQualifier::DISPATCHED)
            ->withQuantity(0.0)
            ->build();

        self::assertEquals('12', $segment->qualifier());
        self::assertEquals('0', $segment->quantity());
        self::assertEquals(0.0, $segment->quantityAsFloat());
    }

    /**
     * @test
     */
    public function preserves_zero_quantity_as_string(): void
    {
        $segment = QTYQuantity::builder()
            ->withQualifier(QTYQualifier::ON_HAND)
            ->withQuantity('0.00')
            ->withMeasureUnit('KGM')
            ->build();

        self::assertEquals('33', $segment->qualifier());
        self::assertEquals('0.00', $segment->quantity());
        self::assertEquals(0.0, $segment->quantityAsFloat());
        self::assertEquals('KGM', $segment->measureUnit());
    }
}
