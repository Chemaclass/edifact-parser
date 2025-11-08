<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments\Qualifier;

use EdifactParser\Segments\Qualifier\QTYQualifier;
use PHPUnit\Framework\TestCase;

final class QTYQualifierTest extends TestCase
{
    /**
     * @test
     */
    public function enum_values_are_correct(): void
    {
        self::assertEquals('1', QTYQualifier::DISCRETE->value);
        self::assertEquals('3', QTYQualifier::CUMULATIVE->value);
        self::assertEquals('11', QTYQualifier::CONSUMER_UNITS->value);
        self::assertEquals('12', QTYQualifier::DISPATCHED->value);
        self::assertEquals('21', QTYQualifier::ORDERED->value);
        self::assertEquals('33', QTYQualifier::ON_HAND->value);
        self::assertEquals('48', QTYQualifier::RECEIVED->value);
        self::assertEquals('47', QTYQualifier::INVOICED->value);
        self::assertEquals('46', QTYQualifier::TO_BE_DELIVERED->value);
        self::assertEquals('192', QTYQualifier::FREE_GOODS->value);
    }

    /**
     * @test
     */
    public function can_be_used_in_match_expressions(): void
    {
        $testCases = [
            QTYQualifier::ORDERED,
            QTYQualifier::DISPATCHED,
            QTYQualifier::INVOICED,
        ];

        $expected = [
            'ordered',
            'dispatched',
            'invoiced',
        ];

        foreach ($testCases as $index => $qualifier) {
            $result = match ($qualifier) {
                QTYQualifier::ORDERED => 'ordered',
                QTYQualifier::DISPATCHED => 'dispatched',
                QTYQualifier::INVOICED => 'invoiced',
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
        $cases = QTYQualifier::cases();

        self::assertCount(10, $cases);
        self::assertContains(QTYQualifier::ORDERED, $cases);
        self::assertContains(QTYQualifier::DISPATCHED, $cases);
    }

    /**
     * @test
     */
    public function can_create_from_string(): void
    {
        $qualifier = QTYQualifier::from('21');

        self::assertSame(QTYQualifier::ORDERED, $qualifier);
    }

    /**
     * @test
     *
     * @psalm-suppress RedundantCondition
     */
    public function try_from_returns_null_for_invalid_value(): void
    {
        $qualifier = QTYQualifier::tryFrom('999');

        /** @psalm-suppress RedundantCondition */
        self::assertNull($qualifier);
    }
}
