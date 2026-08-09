<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Diagnostics;

use EdifactParser\Diagnostics\Diagnostic;
use EdifactParser\Diagnostics\DiagnosticCode;
use EdifactParser\EdifactParser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\Tokenizer\SabasTokenizer;
use EdifactParser\Validation\MessageRuleSet;
use EdifactParser\Validation\MessageValidator;
use EdifactParser\Validation\ValidationViolation;
use PHPUnit\Framework\TestCase;

final class DiagnosticTest extends TestCase
{
    /**
     * @test
     */
    public function carries_a_code_severity_and_location(): void
    {
        $diagnostic = Diagnostic::error(
            DiagnosticCode::SEGMENT_UNTERMINATED,
            'no terminator',
            segmentIndex: 41,
            tag: 'NAD',
            elementPath: 'C186/6060',
        );

        self::assertSame(DiagnosticCode::SEGMENT_UNTERMINATED, $diagnostic->code());
        self::assertSame('no terminator', $diagnostic->message());
        self::assertSame(Diagnostic::SEVERITY_ERROR, $diagnostic->severity());
        self::assertTrue($diagnostic->isError());
        self::assertSame(41, $diagnostic->segmentIndex());
        self::assertSame('NAD', $diagnostic->tag());
        self::assertSame('C186/6060', $diagnostic->elementPath());
    }

    /**
     * @test
     */
    public function warnings_are_not_errors(): void
    {
        $diagnostic = Diagnostic::warning(DiagnosticCode::RELEASE_INVALID, 'odd release');

        self::assertFalse($diagnostic->isError());
        self::assertSame(Diagnostic::SEVERITY_WARNING, $diagnostic->severity());
    }

    /**
     * @test
     */
    public function serialises_to_array_and_json(): void
    {
        $diagnostic = Diagnostic::error(DiagnosticCode::SEGMENT_REQUIRED, 'missing BGM', tag: 'BGM');

        self::assertSame([
            'code' => DiagnosticCode::SEGMENT_REQUIRED,
            'severity' => 'error',
            'message' => 'missing BGM',
            'segmentIndex' => null,
            'tag' => 'BGM',
            'elementPath' => null,
        ], $diagnostic->toArray());

        self::assertSame($diagnostic->toArray(), json_decode($diagnostic->toJson(), true));
    }

    /**
     * @test
     */
    public function renders_a_readable_one_liner(): void
    {
        self::assertSame(
            'error [segment.unterminated] at segment 41 (NAD/C186/6060): no terminator',
            (string) Diagnostic::error(
                DiagnosticCode::SEGMENT_UNTERMINATED,
                'no terminator',
                41,
                'NAD',
                'C186/6060',
            ),
        );

        self::assertSame(
            'error [segment.required]: missing BGM',
            (string) Diagnostic::error(DiagnosticCode::SEGMENT_REQUIRED, 'missing BGM'),
        );

        self::assertSame(
            'error [segment.required] (BGM): missing BGM',
            (string) Diagnostic::error(DiagnosticCode::SEGMENT_REQUIRED, 'missing BGM', tag: 'BGM'),
        );
    }

    /**
     * @test
     */
    public function the_parser_reports_where_tokenizing_failed(): void
    {
        try {
            EdifactParser::createWithDefaultSegments()->parse("UNH+1+ORDERS'BGM+220'NAD+BY");
            self::fail('expected InvalidFile');
        } catch (InvalidFile $e) {
            $diagnostics = $e->getDiagnostics();

            self::assertCount(1, $diagnostics);
            self::assertSame(DiagnosticCode::SEGMENT_UNTERMINATED, $diagnostics[0]->code());
            // Two complete segments were read before the input ran out.
            self::assertSame(2, $diagnostics[0]->segmentIndex());
            self::assertSame('NAD', $diagnostics[0]->tag());
        }
    }

    /**
     * @test
     */
    public function plain_string_errors_still_become_diagnostics(): void
    {
        try {
            (new SabasTokenizer())->tokenize("UNH+1+ORDERS\nUNT+2+1\n");
            self::fail('expected InvalidFile');
        } catch (InvalidFile $e) {
            $diagnostics = $e->getDiagnostics();

            self::assertNotEmpty($diagnostics);
            self::assertSame(DiagnosticCode::TOKENIZE_FAILED, $diagnostics[0]->code());
            self::assertStringContainsString('without terminators', $diagnostics[0]->message());
            // The legacy accessor is untouched.
            self::assertNotEmpty($e->getErrors());
        }
    }

    /**
     * @test
     */
    public function violations_map_onto_stable_codes(): void
    {
        $message = EdifactParser::createWithDefaultSegments()
            ->parse("UNH+1+ORDERS:D:96A:UN'UNT+2+1'")
            ->transactionMessages()[0];

        $rules = MessageRuleSet::forType('ORDERS')->require('BGM')->occurs('DTM', 1, 2);

        $violations = (new MessageValidator())->validate($message, $rules);
        self::assertNotEmpty($violations);

        $codes = array_map(static fn (ValidationViolation $v) => $v->code(), $violations);
        self::assertContains(DiagnosticCode::SEGMENT_REQUIRED, $codes);
        self::assertContains(DiagnosticCode::SEGMENT_CARDINALITY, $codes);

        $diagnostics = (new MessageValidator())->diagnose($message, $rules);
        self::assertSame($codes, array_map(static fn (Diagnostic $d) => $d->code(), $diagnostics));
        self::assertSame($violations[0]->toArray(), $diagnostics[0]->toArray());
    }

    /**
     * @test
     */
    public function an_unmapped_rule_keeps_its_own_name_as_the_code(): void
    {
        $violation = new ValidationViolation('NAD', 'partner-specific', 'nope');

        self::assertSame('partner-specific', $violation->code());
        self::assertSame('NAD', $violation->toDiagnostic()->tag());
    }
}
