<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Validation;

use EdifactParser\EdifactParser;
use EdifactParser\TransactionMessage;
use EdifactParser\Validation\MessageRuleSet;
use EdifactParser\Validation\MessageValidator;
use PHPUnit\Framework\TestCase;

final class MessageValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function a_conforming_message_yields_no_violations(): void
    {
        $rules = MessageRuleSet::forType('IFTMIN')->require('UNH', 'BGM', 'UNT');

        $validator = new MessageValidator();
        self::assertSame([], $validator->validate($this->sampleMessage(), $rules));
        self::assertTrue($validator->isValid($this->sampleMessage(), $rules));
    }

    /**
     * @test
     */
    public function reports_a_missing_required_segment(): void
    {
        $rules = MessageRuleSet::forType('IFTMIN')->require('MOA');

        $violations = (new MessageValidator())->validate($this->sampleMessage(), $rules);

        self::assertCount(1, $violations);
        self::assertSame('MOA', $violations[0]->segmentTag());
        self::assertSame('required', $violations[0]->rule());
    }

    /**
     * @test
     */
    public function reports_cardinality_violations(): void
    {
        // The sample message has 2 NAD segments (CZ and CN) and 1 BGM.
        $rules = MessageRuleSet::forType('IFTMIN')
            ->occurs('NAD', 1, 1)   // too many (2 > 1)
            ->occurs('BGM', 2);     // too few  (1 < 2)

        $violations = (new MessageValidator())->validate($this->sampleMessage(), $rules);

        self::assertCount(2, $violations);
        self::assertSame('cardinality', $violations[0]->rule());
        self::assertSame('cardinality', $violations[1]->rule());
    }

    /**
     * @test
     */
    public function accepts_segments_in_the_expected_sequence(): void
    {
        // In the sample: UNH ... BGM ... UNT appear in that order.
        $rules = MessageRuleSet::forType('IFTMIN')->inSequence('UNH', 'BGM', 'UNT');

        self::assertSame([], (new MessageValidator())->validate($this->sampleMessage(), $rules));
    }

    /**
     * @test
     */
    public function reports_segments_out_of_sequence(): void
    {
        // BGM actually precedes UNT, so requiring UNT before BGM must fail.
        $rules = MessageRuleSet::forType('IFTMIN')->inSequence('UNT', 'BGM');

        $violations = (new MessageValidator())->validate($this->sampleMessage(), $rules);

        self::assertCount(1, $violations);
        self::assertSame('sequence', $violations[0]->rule());
    }
    private function sampleMessage(): TransactionMessage
    {
        return EdifactParser::createWithDefaultSegments()
            ->parseFile(__DIR__ . '/../../../example/edifact-sample.edi')
            ->transactionMessages()[0];
    }
}
