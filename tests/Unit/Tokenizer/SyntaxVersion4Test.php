<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Tokenizer;

use EdifactParser\EdifactParser;
use EdifactParser\Segments\UNBInterchangeHeader;
use EdifactParser\Serializer\EdifactSerializer;
use EdifactParser\Serializer\UnaSeparators;
use EdifactParser\Tokenizer\NativeTokenizer;
use PHPUnit\Framework\TestCase;

/**
 * Syntax version 4 puts the repetition separator in UNA position 5, where version 3 has a
 * reserved placeholder. Everything here must leave syntax 3 behaviour alone.
 */
final class SyntaxVersion4Test extends TestCase
{
    /**
     * @test
     */
    public function a_syntax_3_una_declares_no_repetition_separator(): void
    {
        $una = UnaSeparators::fromUnaSegment("UNA:+.? '");

        self::assertNotNull($una);
        self::assertSame(' ', $una->repetition());
        self::assertFalse($una->hasRepetitionSeparator());
        self::assertSame([':', '+', '.', '?', "'"], [
            $una->component(), $una->element(), $una->decimal(), $una->release(), $una->segmentTerminator(),
        ]);
    }

    /**
     * @test
     */
    public function a_syntax_4_una_declares_one(): void
    {
        $una = UnaSeparators::fromUnaSegment("UNA:+.?*'");

        self::assertNotNull($una);
        self::assertSame('*', $una->repetition());
        self::assertTrue($una->hasRepetitionSeparator());
    }

    /**
     * @test
     */
    public function a_string_that_is_not_a_una_is_rejected(): void
    {
        self::assertNull(UnaSeparators::fromUnaSegment('UNA:+.?'));
        self::assertNull(UnaSeparators::fromUnaSegment("UNB+UNOC:3'"));
    }

    /**
     * @test
     */
    public function the_una_round_trips(): void
    {
        self::assertSame("UNA:+.? '", UnaSeparators::default()->toUnaSegment());
        self::assertSame("UNA:+.?*'", UnaSeparators::syntax4()->toUnaSegment());
        self::assertSame("UNA:+.?~'", UnaSeparators::default()->withRepetition('~')->toUnaSegment());

        $v4 = UnaSeparators::fromUnaSegment(UnaSeparators::syntax4()->toUnaSegment());
        self::assertSame('*', $v4?->repetition());
    }

    /**
     * @test
     */
    public function a_declared_separator_splits_repeats(): void
    {
        $segments = (new NativeTokenizer())->tokenize("UNA:+.?*'RFF+CU:A*CU:B'");

        self::assertSame([['RFF', [['CU', 'A'], ['CU', 'B']]]], $segments);
    }

    /**
     * @test
     */
    public function simple_values_repeat_too(): void
    {
        $segments = (new NativeTokenizer())->tokenize("UNA:+.?*'FTX+AAI+a*b*c'");

        self::assertSame([['FTX', 'AAI', ['a', 'b', 'c']]], $segments);
    }

    /**
     * @test
     */
    public function a_released_repetition_separator_is_data(): void
    {
        $segments = (new NativeTokenizer())->tokenize("UNA:+.?*'FTX+AAI+2?*3=6'");

        self::assertSame([['FTX', 'AAI', '2*3=6']], $segments);
    }

    /**
     * @test
     */
    public function syntax_3_leaves_the_repetition_character_as_ordinary_data(): void
    {
        // The same payload under a syntax 3 UNA: '*' means nothing, so it stays put.
        $segments = (new NativeTokenizer())->tokenize("UNA:+.? 'FTX+AAI+a*b*c'");

        self::assertSame([['FTX', 'AAI', 'a*b*c']], $segments);
    }

    /**
     * @test
     */
    public function no_una_at_all_leaves_it_as_ordinary_data(): void
    {
        self::assertSame([['FTX', 'AAI', 'a*b']], (new NativeTokenizer())->tokenize("FTX+AAI+a*b'"));
    }

    /**
     * @test
     */
    public function the_separator_can_be_configured_without_a_una(): void
    {
        $tokenizer = new NativeTokenizer(repetition: '*');

        self::assertSame([['FTX', 'AAI', ['a', 'b']]], $tokenizer->tokenize("FTX+AAI+a*b'"));
    }

    /**
     * @test
     */
    public function an_unterminated_repeat_is_still_rejected(): void
    {
        $this->expectException(\EdifactParser\Exception\InvalidFile::class);

        (new NativeTokenizer())->tokenize("UNA:+.?*'FTX+AAI+a*b");
    }

    /**
     * @test
     */
    public function the_serializer_escapes_a_declared_repetition_separator(): void
    {
        $v4 = new EdifactSerializer(UnaSeparators::syntax4());
        $rendered = $v4->serializeSegment(new \EdifactParser\Segments\FTXFreeText(['FTX', 'AAI', '2*3']));

        self::assertSame("FTX+AAI+2?*3'", $rendered);

        // Under syntax 3 the reserved position is not a delimiter, so nothing is escaped.
        $v3 = new EdifactSerializer(UnaSeparators::default());
        self::assertSame(
            "FTX+AAI+2*3'",
            $v3->serializeSegment(new \EdifactParser\Segments\FTXFreeText(['FTX', 'AAI', '2*3'])),
        );
    }

    /**
     * @test
     */
    public function a_syntax_4_interchange_round_trips_through_the_parser(): void
    {
        $edi = "UNA:+.?*'UNB+UNOC:4+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'FTX+AAI+2?*3'UNT+3+1'UNZ+1+1'";

        $result = EdifactParser::createWithDefaultSegments()->parse($edi);
        $message = $result->transactionMessages()[0];

        self::assertSame('ORDERS', $message->messageType());
        self::assertSame('2*3', $message->segmentByTagAndSubId('FTX', 'AAI')?->rawValues()[2]);

        $unb = $result->globalSegments()->segmentByTagAndSubId('UNB', 'UNOC');
        self::assertInstanceOf(UNBInterchangeHeader::class, $unb);
        self::assertSame('4', $unb->syntaxVersionNumber());
    }
}
