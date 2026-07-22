<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Validation;

use EdifactParser\EdifactParser;
use EdifactParser\Validation\MessageRuleSets;
use EdifactParser\Validation\MessageValidator;
use PHPUnit\Framework\TestCase;

final class MessageRuleSetsTest extends TestCase
{
    /**
     * @test
     */
    public function the_iftmin_sample_conforms_to_the_predefined_iftmin_rule_set(): void
    {
        $message = EdifactParser::createWithDefaultSegments()
            ->parseFile(__DIR__ . '/../../../example/edifact-sample.edi')
            ->transactionMessages()[0];

        self::assertTrue((new MessageValidator())->isValid($message, MessageRuleSets::iftmin()));
    }

    /**
     * @test
     */
    public function orders_rule_set_flags_a_message_missing_the_mandatory_bgm(): void
    {
        $message = EdifactParser::createWithDefaultSegments()
            ->parse("UNH+1+ORDERS:D:96A:UN'UNT+2+1'")
            ->transactionMessages()[0];

        $violations = (new MessageValidator())->validate($message, MessageRuleSets::orders());

        self::assertNotEmpty($violations);
        self::assertSame('BGM', $violations[0]->segmentTag());
        self::assertSame('required', $violations[0]->rule());
    }
}
