<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\EdifactParser;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\UNBInterchangeHeader;
use PHPUnit\Framework\TestCase;

final class ParserResultTest extends TestCase
{
    /**
     * @psalm-suppress InvalidArrayOffset
     */
    public function test_retrieve_segments_across_global_and_messages(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNB+UNOC:3+1234567890123+9876543210987+250506:1300+ORDER001'
UNH+1+ORDERS:D:96A:UN:EAN008'
CNT+2:2'
UNT+13+1'
UNZ+1+ORDER001'
EDI;
        $parserResult = EdifactParser::createWithDefaultSegments()->parse($fileContent);

        $unb = $parserResult->segmentByTagAndSubId('UNB', 'UNOC');
        self::assertInstanceOf(UNBInterchangeHeader::class, $unb);

        $cntSegments = $parserResult->segmentsByTag('CNT');
        self::assertArrayHasKey('2', $cntSegments);
        self::assertInstanceOf(CNTControl::class, $cntSegments['2']);
    }
}
