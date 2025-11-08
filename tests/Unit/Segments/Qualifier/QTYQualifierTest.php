<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments\Qualifier;

use EdifactParser\Segments\Qualifier\QTYQualifier;
use Error;
use PHPUnit\Framework\TestCase;

final class QTYQualifierTest extends TestCase
{
    /**
     * @test
     */
    public function const_values_are_correct(): void
    {
        self::assertEquals('1', QTYQualifier::DISCRETE);
        self::assertEquals('3', QTYQualifier::CUMULATIVE);
        self::assertEquals('11', QTYQualifier::CONSUMER_UNITS);
        self::assertEquals('12', QTYQualifier::DISPATCHED);
        self::assertEquals('21', QTYQualifier::ORDERED);
        self::assertEquals('33', QTYQualifier::ON_HAND);
        self::assertEquals('48', QTYQualifier::RECEIVED);
        self::assertEquals('47', QTYQualifier::INVOICED);
        self::assertEquals('46', QTYQualifier::TO_BE_DELIVERED);
        self::assertEquals('192', QTYQualifier::FREE_GOODS);
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
     *
     * @psalm-suppress InaccessibleMethod
     */
    public function cannot_be_instantiated(): void
    {
        $this->expectException(Error::class);

        new QTYQualifier();
    }
}
