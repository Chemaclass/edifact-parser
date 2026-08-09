<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Tokenizer;

use EdifactParser\EdifactParser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\StreamingParser;
use EdifactParser\Tokenizer\NativeTokenizer;
use PHPUnit\Framework\TestCase;

final class NativeTokenizerTest extends TestCase
{
    /**
     * @test
     */
    public function non_ascii_values_survive(): void
    {
        // The default tokenizer strips every byte in \x80-\xFF, turning this into
        // "Mller GmbH". Copying bytes instead of filtering them is the whole point.
        $segments = (new NativeTokenizer())->tokenize("NAD+BY+++Müller GmbH'");

        self::assertSame([['NAD', 'BY', '', '', 'Müller GmbH']], $segments);
    }

    /**
     * @test
     */
    public function latin1_bytes_survive_untouched(): void
    {
        $latin1 = (string) mb_convert_encoding('Müller', 'ISO-8859-1', 'UTF-8');

        $segments = (new NativeTokenizer())->tokenize("NAD+BY+++{$latin1}'");

        self::assertSame($latin1, $segments[0][4]);
    }

    /**
     * @test
     */
    public function content_without_a_terminator_is_rejected(): void
    {
        $this->expectException(InvalidFile::class);

        (new NativeTokenizer())->tokenize("UNH+1+ORDERS\nUNT+2+1\n");
    }

    /**
     * @test
     */
    public function a_half_finished_trailing_segment_is_rejected(): void
    {
        $this->expectException(InvalidFile::class);

        (new NativeTokenizer())->tokenize("UNH+1+ORDERS:D:96A:UN'NAD+BY");
    }

    /**
     * @test
     */
    public function a_trailing_composite_without_a_terminator_is_rejected(): void
    {
        $this->expectException(InvalidFile::class);

        (new NativeTokenizer())->tokenize("UNH+1+ORDERS:D:96A:UN'NAD+a:b");
    }

    /**
     * @test
     */
    public function trailing_blanks_after_the_last_terminator_are_not_an_error(): void
    {
        $segments = (new NativeTokenizer())->tokenize("UNH+1+ORDERS'  \n\r\n  ");

        self::assertSame([['UNH', '1', 'ORDERS']], $segments);
    }

    /**
     * @test
     */
    public function a_release_char_at_the_very_end_releases_nothing(): void
    {
        // Malformed but must not read past the end of the string.
        $this->expectException(InvalidFile::class);

        (new NativeTokenizer())->tokenize("UNH+1+ORDERS'NAD+BY?");
    }

    /**
     * @test
     */
    public function empty_content_yields_no_segments(): void
    {
        self::assertSame([], (new NativeTokenizer())->tokenize(''));
        self::assertSame([], (new NativeTokenizer())->tokenize("   \n  "));
    }

    /**
     * @test
     */
    public function a_truncated_una_is_not_treated_as_a_service_string_advice(): void
    {
        // Too short to be a UNA, so it is just a segment like any other.
        self::assertSame([['UNA', 'x']], (new NativeTokenizer())->tokenize("UNA+x'"));
    }

    /**
     * @test
     */
    public function delimiters_can_be_set_without_a_una(): void
    {
        $tokenizer = new NativeTokenizer(component: '#', element: '*', release: '^', segmentTerminator: '~');

        self::assertSame(
            [['NAD', 'BY', ['a', 'b*c']]],
            $tokenizer->tokenize('NAD*BY*a#b^*c~'),
        );
    }

    /**
     * @test
     */
    public function a_una_overrides_the_configured_delimiters(): void
    {
        $tokenizer = new NativeTokenizer(component: '#', element: '*', release: '^', segmentTerminator: '~');

        self::assertSame(
            [['NAD', 'BY', ['a', 'b']]],
            $tokenizer->tokenize("UNA:+.? 'NAD+BY+a:b'"),
        );
    }

    /**
     * @test
     */
    public function it_is_the_default_so_non_ascii_survives_a_plain_parse(): void
    {
        $edi = "UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'NAD+BY+++Müller GmbH'UNT+3+1'UNZ+1+1'";

        $buyer = EdifactParser::createWithDefaultSegments()
            ->parse($edi)
            ->transactionMessages()[0]
            ->segmentByTagAndSubId('NAD', 'BY');

        self::assertInstanceOf(NADNameAddress::class, $buyer);
        self::assertSame('Müller GmbH', $buyer->name());
    }

    /**
     * @test
     */
    public function the_parser_accepts_it_as_a_drop_in(): void
    {
        $edi = "UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'NAD+BY+++Müller GmbH'UNT+3+1'UNZ+1+1'";

        $message = EdifactParser::createWithDefaultSegments(tokenizer: new NativeTokenizer())
            ->parse($edi)
            ->transactionMessages()[0];

        $buyer = $message->segmentByTagAndSubId('NAD', 'BY');

        self::assertInstanceOf(NADNameAddress::class, $buyer);
        self::assertSame('Müller GmbH', $buyer->name());
    }

    /**
     * @test
     */
    public function the_streaming_parser_accepts_it_as_a_drop_in(): void
    {
        $path = (string) tempnam(sys_get_temp_dir(), 'edi');
        file_put_contents($path, "UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'BGM+220'UNT+3+1'UNZ+1+1'");

        try {
            $messages = [];
            foreach (StreamingParser::createWithDefaultSegments(tokenizer: new NativeTokenizer())->parseFile($path) as $message) {
                $messages[] = $message;
            }

            self::assertCount(1, $messages);
            self::assertSame('ORDERS', $messages[0]->messageType());
        } finally {
            @unlink($path);
        }
    }
}
