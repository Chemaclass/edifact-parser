<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Functional;

use EdifactParser\EdifactParser;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;
use PHPUnit\Framework\TestCase;

final class EdifactParserTest extends TestCase
{
    /**
     * @test
     */
    public function parse_more_than_one_message(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNB+UNOC:1+2:3'
UNH+1+IFTMIN:S:93A:UN:PN001'
UNT+19+1'
UNH+2+IFTMIN:S:94A:UN:PN002'
UNT+19+2'
UNH+3+IFTMIN:S:94A:UN:PN003'
UNT+19+3'
UNZ+3+4'
EDI;
        $parserResult = EdifactParser::createWithDefaultSegments()->parse($fileContent);

        self::assertCount(2, $parserResult->globalSegments());
        self::assertCount(3, $parserResult->transactionMessages());
    }

    /**
     * @test
     */
    public function extract_values_from_message(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+IFTMIN:S:93A:UN:PN001'
CNT+7:0.1:KGM'
CNT+11:1:PCE'
UNT+19+1'
UNZ+1+3'
EDI;
        $parserResult = EdifactParser::createWithDefaultSegments()->parse($fileContent);
        self::assertCount(1, $parserResult->transactionMessages());
        $message = $parserResult->transactionMessages()[0];

        /** @var UNHMessageHeader $unh */
        $unh = $message->segmentByTagAndSubId('UNH', '1');
        self::assertEquals(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']], $unh->rawValues());

        /** @var CNTControl $cnt7 */
        $cnt7 = $message->segmentByTagAndSubId('CNT', '7');
        self::assertEquals(['CNT', ['7', '0.1', 'KGM']], $cnt7->rawValues());

        /** @var CNTControl $cnt11 */
        $cnt11 = $message->segmentByTagAndSubId('CNT', '11');
        self::assertEquals(['CNT', ['11', '1', 'PCE']], $cnt11->rawValues());

        /** @var UNTMessageFooter $unt */
        $unt = $message->segmentByTagAndSubId('UNT', '19');
        self::assertEquals(['UNT', '19', '1'], $unt->rawValues());
    }

    /**
     * @test
     */
    public function use_a_custom_segment_factory(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+IFTMIN:S:93A:UN:PN001'
CUSTOM+anyKey+whatever:value:9'
CNT+11:1:PCE'
UNT+19+1'
UNZ+1+3'
EDI;
        $parser = new EdifactParser(new TestingSegmentFactory('CUSTOM'));
        $parserResult = $parser->parse($fileContent);
        self::assertCount(1, $parserResult->transactionMessages());
        $message = $parserResult->transactionMessages()[0];

        /** @var SegmentInterface $custom */
        $custom = $message->segmentByTagAndSubId('CUSTOM', 'anyKey');
        self::assertEquals(['CUSTOM', 'anyKey', ['whatever', 'value', '9']], $custom->rawValues());

        /** @var CNTControl $cnt11 */
        $cnt11 = $message->segmentByTagAndSubId('CNT', '11');
        self::assertEquals(['CNT', ['11', '1', 'PCE']], $cnt11->rawValues());
    }

    /**
     * @test
     */
    public function handles_unknown_segments(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+IFTMIN:S:93A:UN:PN001'
UNK+first+23'
UNK+second+52'
CNT+11:1:PCE'
UNT+19+1'
UNZ+1+3'
EDI;
        $parserResult = EdifactParser::createWithDefaultSegments()->parse($fileContent);
        $message = $parserResult->transactionMessages()[0];

        self::assertNotNull($message->segmentByTagAndSubId('UNK', 'first'));
        self::assertNotNull($message->segmentByTagAndSubId('UNK', 'second'));
    }

    /**
     * @test
     */
    public function handles_unknown_segments_in_line_items(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+IFTMIN:S:93A:UN:PN001'
LIN+1'
UNK+first+23'
LIN+2'
UNK+first+13'
UNS+S'
CNT+11:1:PCE'
UNT+19+1'
UNZ+1+3'
EDI;
        $parserResult = EdifactParser::createWithDefaultSegments()->parse($fileContent);
        $message = $parserResult->transactionMessages()[0];

        self::assertNotNull($message->lineItemById(1)?->segmentByTagAndSubId('UNK', 'first'));
        self::assertNotNull($message->lineItemById(2)?->segmentByTagAndSubId('UNK', 'first'));
    }
}
