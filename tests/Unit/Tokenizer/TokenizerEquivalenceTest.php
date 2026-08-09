<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Tokenizer;

use EdifactParser\Tokenizer\NativeTokenizer;
use EdifactParser\Tokenizer\SabasTokenizer;
use PHPUnit\Framework\TestCase;

use function count;

/**
 * The native tokenizer is only safe to offer if it agrees with the reference one. These
 * cases are ASCII-only on purpose: the two intentionally differ on non-ASCII input, where
 * sabas strips bytes and the native tokenizer preserves them.
 */
final class TokenizerEquivalenceTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider interchanges
     */
    public function native_agrees_with_the_reference_tokenizer(string $edi): void
    {
        self::assertSame(
            (new SabasTokenizer())->tokenize($edi),
            (new NativeTokenizer())->tokenize($edi),
        );
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function interchanges(): iterable
    {
        yield 'plain interchange' => ["UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'UNT+2+1'UNZ+1+1'"];
        yield 'with UNA' => ["UNA:+.? 'UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'UNT+2+1'UNZ+1+1'"];
        yield 'custom UNA delimiters' => ['UNA#*.^ ~UNB*UNOC#3*S*R*240101#1200*1~UNH*1*ORDERS#D#96A#UN~UNT*2*1~UNZ*1*1~'];
        yield 'newline separated' => ["UNH+1+ORDERS:D:96A:UN'\nBGM+220'\nUNT+3+1'\n"];
        yield 'crlf separated' => ["UNH+1+ORDERS:D:96A:UN'\r\nBGM+220'\r\nUNT+3+1'\r\n"];
        yield 'leading blank lines' => ["\n\n  UNH+1+ORDERS:D:96A:UN'UNT+2+1'"];
        yield 'empty elements' => ["UNH+1+ORDERS:D:96A:UN'NAD+BY+++++'UNT+3+1'"];
        yield 'trailing empty component' => ["UNH+1+ORDERS:D:96A:UN'CNT+2:0.5:'UNT+3+1'"];
        yield 'single component stays a string' => ["UNH+1+ORDERS:D:96A:UN'RFF+CU'UNT+3+1'"];
        yield 'released element separator' => ["UNH+1+ORDERS:D:96A:UN'FTX+AAI+++10?+10=20'UNT+3+1'"];
        yield 'released release char' => ["UNH+1+ORDERS:D:96A:UN'FTX+AAI+++a??b'UNT+3+1'"];
        yield 'released component separator' => ["UNH+1+ORDERS:D:96A:UN'FTX+AAI+++a?:b'UNT+3+1'"];
        yield 'released terminator' => ["UNH+1+ORDERS:D:96A:UN'FTX+AAI+++a?'b'UNT+3+1'"];
        yield 'consecutive releases' => ["UNH+1+ORDERS:D:96A:UN'FTX+AAI+++??????'UNT+3+1'"];
        // A release before a non-delimiter is malformed; both characters stay as data.
        yield 'release before a plain character' => ["UNH+1+ORDERS:D:96A:UN'COM+john?@example.com:EM'UNT+3+1'"];
        yield 'release before a digit' => ["UNH+1+ORDERS:D:96A:UN'FTX+AAI+++?9?a?Z'UNT+3+1'"];
        yield 'deep composite' => ["UNH+1+ORDERS:D:96A:UN'NAD+BY+a:b:c:d:e:f+++x'UNT+3+1'"];
        yield 'numeric values' => ["UNH+1+ORDERS:D:96A:UN'QTY+21:100.5:PCE'UNT+3+1'"];
        yield 'many messages' => [
            "UNB+UNOC:3+S+R+240101:1200+1'"
            . str_repeat("UNH+1+ORDERS:D:96A:UN'BGM+220'DTM+137:20240101:102'UNT+4+1'", 25)
            . "UNZ+25+1'",
        ];
        yield 'the shipped sample file' => [(string) file_get_contents(__DIR__ . '/../../../example/edifact-sample.edi')];
    }

    /**
     * @test
     */
    public function native_agrees_with_the_reference_tokenizer_on_a_generated_corpus(): void
    {
        // Deterministic pseudo-random segments built from the alphabet that actually
        // exercises the scanner: separators, release sequences, empties and blanks.
        mt_srand(20260809);

        $fragments = ['A', 'BC', '12', '', 'x y', '?+', '??', '?:', "?'", '?@', '?9', '0.5', 'ZZZ'];
        $tags = ['NAD', 'LIN', 'QTY', 'PRI', 'RFF', 'FTX', 'DTM', 'MOA'];

        $edi = "UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'";

        for ($segment = 0; $segment < 200; ++$segment) {
            $edi .= $tags[mt_rand(0, count($tags) - 1)];

            for ($element = 0, $elements = mt_rand(1, 5); $element < $elements; ++$element) {
                $edi .= '+';

                for ($component = 0, $components = mt_rand(1, 4); $component < $components; ++$component) {
                    if ($component > 0) {
                        $edi .= ':';
                    }
                    $edi .= $fragments[mt_rand(0, count($fragments) - 1)];
                }
            }

            $edi .= "'" . (mt_rand(0, 3) === 0 ? "\n" : '');
        }

        $edi .= "UNT+202+1'UNZ+1+1'";

        self::assertSame(
            (new SabasTokenizer())->tokenize($edi),
            (new NativeTokenizer())->tokenize($edi),
        );
    }
}
