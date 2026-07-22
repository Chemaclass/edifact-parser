<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\EdifactParser;
use PHPUnit\Framework\TestCase;

final class FunctionalGroupTest extends TestCase
{
    private const WITH_GROUPS = <<<'EDI'
        UNB+UNOC:3+SENDER+RECIPIENT+20191011:1200+REF'
        UNG+ORDERS+S1+R1+20191011:1200+1+UN+D:96A'
        UNH+1+ORDERS:D:96A:UN'
        BGM+220'
        UNT+3+1'
        UNH+2+ORDERS:D:96A:UN'
        BGM+221'
        UNT+3+2'
        UNE+2+1'
        UNG+INVOIC+S1+R1+20191011:1200+2+UN+D:96A'
        UNH+3+INVOIC:D:96A:UN'
        BGM+380'
        UNT+3+3'
        UNE+1+2'
        UNZ+3+REF'
        EDI;

    /**
     * @test
     */
    public function exposes_functional_groups_while_keeping_messages_flat(): void
    {
        $result = EdifactParser::createWithDefaultSegments()->parse(self::WITH_GROUPS);

        // Flat view is preserved (backward compatible)
        self::assertCount(3, $result->transactionMessages());

        $groups = $result->functionalGroups();
        self::assertCount(2, $groups);

        self::assertSame('ORDERS', $groups[0]->messageType());
        self::assertSame('1', $groups[0]->header()->groupReference());
        self::assertCount(2, $groups[0]->messages());
        self::assertSame('2', $groups[0]->trailer()?->controlCount());

        self::assertSame('INVOIC', $groups[1]->messageType());
        self::assertCount(1, $groups[1]->messages());
    }

    /**
     * @test
     */
    public function interchange_without_groups_has_no_functional_groups(): void
    {
        $result = EdifactParser::createWithDefaultSegments()
            ->parseFile(__DIR__ . '/../../example/edifact-sample.edi');

        self::assertSame([], $result->functionalGroups());
        self::assertNotEmpty($result->transactionMessages());
    }
}
