<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Functional;

use EdifactParser\EdifactParser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\ReadModel\Segment;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;
use PHPUnit\Framework\TestCase;

final class EdifactParserTest extends TestCase
{
    /** @test */
    public function invalidFileDueToANonPrintableChar(): void
    {
        $fileContent = <<<EDI
\xE2\x80\xAF
EDI;
        $this->expectException(InvalidFile::class);
        EdifactParser::create()->parse($fileContent);
    }

    /** @test */
    public function parseMoreThanOneMessage(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+IFTMIN:S:93A:UN:PN001'
UNT+19+1'
UNH+2+IFTMIN:S:94A:UN:PN002'
UNT+19+2'
UNH+3+IFTMIN:S:94A:UN:PN003'
UNT+19+3'
UNZ+3+4'
EDI;
        $transactionResult = EdifactParser::create()->parse($fileContent);
        self::assertCount(3, $transactionResult);
    }

    /** @test */
    public function extractValuesFromMessage(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+IFTMIN:S:93A:UN:PN001'
CNT+7:0.1:KGM'
CNT+11:1:PCE'
UNT+19+1'
UNZ+1+3'
EDI;

        $transactionResult = EdifactParser::create()->parse($fileContent);
        self::assertCount(1, $transactionResult);
        /** @var array<string, array<string,SegmentInterface>> $segments */
        $segments = $transactionResult[0];

        /** @var UNHMessageHeader $unh */
        $unh = $segments[UNHMessageHeader::class]['1'];
        self::assertEquals(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']], $unh->rawValues());

        /** @var CNTControl $cnt7 */
        $cnt7 = $segments[CNTControl::class]['7'];
        self::assertEquals(['CNT', ['7', '0.1', 'KGM']], $cnt7->rawValues());

        /** @var CNTControl $cnt11 */
        $cnt11 = $segments[CNTControl::class]['11'];
        self::assertEquals(['CNT', ['11', '1', 'PCE']], $cnt11->rawValues());

        /** @var UNTMessageFooter $unt */
        $unt = $segments[UNTMessageFooter::class]['19'];
        self::assertEquals(['UNT', '19', '1'], $unt->rawValues());
    }

    /** @test */
    public function useACustomSegmentFactory(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+IFTMIN:S:93A:UN:PN001'
CUSTOM+anyKey+whatever:value:9'
CNT+11:1:PCE'
UNT+19+1'
UNZ+1+3'
EDI;
        $parser = EdifactParser::create(new TestingSegmentFactory('CUSTOM'));
        $transactionResult = $parser->parse($fileContent);
        self::assertCount(1, $transactionResult);
        $segments = $transactionResult[0];

        /** @var SegmentInterface $custom */
        $custom = $segments['CUSTOM']['anyKey'];
        self::assertEquals(['CUSTOM', 'anyKey', ['whatever', 'value', '9']], $custom->rawValues());

        /** @var CNTControl $cnt11 */
        $cnt11 = $segments[CNTControl::class]['11'];
        self::assertEquals(['CNT', ['11', '1', 'PCE']], $cnt11->rawValues());
    }
}
