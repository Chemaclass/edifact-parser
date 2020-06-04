<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EDI\Parser;
use EdifactParser\SegmentList;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;
use EdifactParser\TransactionMessage;
use PHPUnit\Framework\TestCase;

final class TransactionMessageTest extends TestCase
{
    /** @test */
    public function segmentsInOneMessage(): void
    {
        $fileContent = "UNH+1+IFTMIN:S:93A:UN:PN001'\nUNT+19+1'";

        self::assertEquals([
            new TransactionMessage([
                UNHMessageHeader::class => [
                    '1' => new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                ],
                UNTMessageFooter::class => [
                    '19' => new UNTMessageFooter(['UNT', '19', '1']),
                ],
            ]),
        ], $this->transactionMessages($fileContent));
    }

    /** @test */
    public function segmentsInTwoMessages(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+IFTMIN:S:93A:UN:PN001'
UNT+19+1'
UNH+2+IFTMIN:S:94A:UN:PN002'
UNT+19+2'
EDI;
        self::assertEquals([
            new TransactionMessage([
                UNHMessageHeader::class => [
                    '1' => new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                ],
                UNTMessageFooter::class => [
                    '19' => new UNTMessageFooter(['UNT', '19', '1']),
                ],
            ]),
            new TransactionMessage([
                UNHMessageHeader::class => [
                    '2' => new UNHMessageHeader(['UNH', '2', ['IFTMIN', 'S', '94A', 'UN', 'PN002']]),
                ],
                UNTMessageFooter::class => [
                    '19' => new UNTMessageFooter(['UNT', '19', '2']),
                ],
            ]),
        ], $this->transactionMessages($fileContent));
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
EDI;
        self::assertEquals([
            new TransactionMessage([
                UNHMessageHeader::class => [
                    '1' => new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                ],
                UNTMessageFooter::class => [
                    '19' => new UNTMessageFooter(['UNT', '19', '1']),
                ],
                CNTControl::class => [
                    '7' => new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
                    '11' => new CNTControl(['CNT', ['11', '1', 'PCE']]),
                    '15' => new CNTControl(['CNT', ['15', '0.068224', 'MTQ']]),
                ],
            ]),
        ], $this->transactionMessages($fileContent));
    }

    private function transactionMessages(string $fileContent): array
    {
        return TransactionMessage::groupSegmentsByMessage(
            ...SegmentList::factory()->fromRaw((new Parser($fileContent))->get())
        );
    }
}
