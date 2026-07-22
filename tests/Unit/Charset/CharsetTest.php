<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Charset;

use EdifactParser\Charset\Charset;
use EdifactParser\EdifactParser;
use EdifactParser\Segments\UNBInterchangeHeader;
use PHPUnit\Framework\TestCase;

final class CharsetTest extends TestCase
{
    /**
     * @test
     */
    public function maps_syntax_identifiers_to_encodings(): void
    {
        self::assertSame('ISO-8859-1', Charset::encodingFor('UNOC'));
        self::assertSame('ISO-8859-1', Charset::encodingFor('unoc')); // case-insensitive
        self::assertSame('UTF-8', Charset::encodingFor('UNOY'));
        self::assertSame('UTF-8', Charset::encodingFor('WHATEVER')); // unknown falls back to UTF-8
    }

    /**
     * @test
     */
    public function decodes_latin1_bytes_to_utf8(): void
    {
        // 0xE9 is 'é' in ISO-8859-1; in UTF-8 it is the two bytes 0xC3 0xA9.
        self::assertSame("\xC3\xA9", Charset::toUtf8("\xE9", 'UNOC'));

        // UTF-8 input is returned unchanged.
        self::assertSame("\xC3\xA9", Charset::toUtf8("\xC3\xA9", 'UNOY'));
    }

    /**
     * @test
     */
    public function unb_exposes_its_character_encoding(): void
    {
        $unb = EdifactParser::createWithDefaultSegments()
            ->parseFile(__DIR__ . '/../../../example/edifact-sample.edi')
            ->globalSegments()
            ->segmentByTagAndSubId('UNB', 'UNOC');

        self::assertInstanceOf(UNBInterchangeHeader::class, $unb);
        self::assertSame('ISO-8859-1', $unb->characterEncoding());
    }
}
