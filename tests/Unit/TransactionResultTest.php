<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EDI\Parser;
use EdifactParser\SegmentedValues;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UnknownSegment;
use EdifactParser\Segments\UNTMessageFooter;
use EdifactParser\TransactionMessage;
use EdifactParser\TransactionResult;
use PHPUnit\Framework\TestCase;

final class TransactionResultTest extends TestCase
{
    /** @test */
    public function oneMessage(): void
    {
        $fileContent = "UNH+1+IFTMIN:S:93A:UN:PN001'\nUNT+19+1'";

        self::assertEquals([
            new TransactionMessage([
                new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                new UNTMessageFooter(['UNT', '19', '1']),
            ]),
        ], $this->resultFactory($fileContent)->messages());
    }

    /** @test */
    public function twoMessages(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+IFTMIN:S:93A:UN:PN001'
UNT+19+1'
UNH+2+IFTMIN:S:94A:UN:PN002'
UNT+19+2'
UNZ+2+3'
EDI;
        self::assertEquals([
            new TransactionMessage([
                new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                new UNTMessageFooter(['UNT', '19', '1']),
            ]),
            new TransactionMessage([
                new UNHMessageHeader(['UNH', '2', ['IFTMIN', 'S', '94A', 'UN', 'PN002']]),
                new UNTMessageFooter(['UNT', '19', '2']),
                new UnknownSegment(['UNZ', '2', '3']),
            ]),
        ], $this->resultFactory($fileContent)->messages());
    }

    /** @test */
    public function oneMessageWithMultipleSegmentsWithTheSameName(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+IFTMIN:S:93A:UN:PN001'
CNT+7:0.1:KGM'
CNT+11:1:PCE'
CNT+15:0.068224:MTQ'
UNT+19+1'
UNZ+2+3'
EDI;
        self::assertEquals([
            new TransactionMessage([
                new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                new UNTMessageFooter(['UNT', '19', '1']),
                new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
                new CNTControl(['CNT', ['11', '1', 'PCE']]),
                new CNTControl(['CNT', ['15', '0.068224', 'MTQ']]),
                new UnknownSegment(['UNZ', '2', '3']),
            ]),
        ], $this->resultFactory($fileContent)->messages());
    }

    private function resultFactory(string $fileContent): TransactionResult
    {
        return TransactionResult::fromSegmentedValues(
            SegmentedValues::factory()->fromRaw((new Parser($fileContent))->get())
        );
    }
}
