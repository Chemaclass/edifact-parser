<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EDI\Parser;

use EdifactParser\SegmentedValues;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\MEADimensions;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UnknownSegment;
use EdifactParser\Segments\UNTMessageFooter;
use EdifactParser\TransactionMessage;
use PHPUnit\Framework\TestCase;

final class TransactionMessageTest extends TestCase
{
    /** @test */
    public function twoSegmentsWithDifferentNames(): void
    {
        $message = TransactionMessage::fromSegments(
            new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
            new MEADimensions(['MEA', 'WT', 'G', ['KGM', '0.1']]),
        );

        self::assertEquals(new TransactionMessage([
            CNTControl::class => [
                '7' => new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
            ],
            MEADimensions::class => [
                'WT' => new MEADimensions(['MEA', 'WT', 'G', ['KGM', '0.1']]),
            ],
        ]), $message);
    }

    /** @test */
    public function twoSegmentsWithTheSameName(): void
    {
        $message = TransactionMessage::fromSegments(
            new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
            new UNTMessageFooter(['UNT', '19', '1']),
            new MEADimensions(['MEA', 'WT', 'G', ['KGM', '0.1']]),
            new MEADimensions(['MEA', 'VOL', '', ['MTQ', '0.06822']]),
        );

        self::assertEquals(new TransactionMessage([
            UNHMessageHeader::class => [
                '1' => new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
            ],
            UNTMessageFooter::class => [
                '19' => new UNTMessageFooter(['UNT', '19', '1']),
            ],
            MEADimensions::class => [
                'WT' => new MEADimensions(['MEA', 'WT', 'G', ['KGM', '0.1']]),
                'VOL' => new MEADimensions(['MEA', 'VOL', '', ['MTQ', '0.06822']]),
            ],
        ]), $message);
    }

    /** @test */
    public function moreThanTwoSegmentsWithTheSameName(): void
    {
        $message = TransactionMessage::fromSegments(
            new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
            new UNTMessageFooter(['UNT', '19', '1']),
            new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
            new CNTControl(['CNT', ['11', '1', 'PCE']]),
            new CNTControl(['CNT', ['15', '0.068224', 'MTQ']]),
        );

        self::assertEquals(new TransactionMessage([
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
        ]), $message);
    }

    /** @test */
    public function oneMessage(): void
    {
        $fileContent = "UNH+1+IFTMIN:S:93A:UN:PN001'\nUNT+19+1'";

        self::assertEquals([
            TransactionMessage::fromSegments(
                new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                new UNTMessageFooter(['UNT', '19', '1']),
            ),
        ], $this->resultFactory($fileContent));
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
            TransactionMessage::fromSegments(
                new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                new UNTMessageFooter(['UNT', '19', '1']),
            ),
            TransactionMessage::fromSegments(
                new UNHMessageHeader(['UNH', '2', ['IFTMIN', 'S', '94A', 'UN', 'PN002']]),
                new UNTMessageFooter(['UNT', '19', '2']),
                new UnknownSegment(['UNZ', '2', '3']),
            ),
        ], $this->resultFactory($fileContent));
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
            TransactionMessage::fromSegments(
                new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                new UNTMessageFooter(['UNT', '19', '1']),
                new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
                new CNTControl(['CNT', ['11', '1', 'PCE']]),
                new CNTControl(['CNT', ['15', '0.068224', 'MTQ']]),
                new UnknownSegment(['UNZ', '2', '3']),
            ),
        ], $this->resultFactory($fileContent));
    }

    private function resultFactory(string $fileContent): array
    {
        return TransactionMessage::fromSegmentedValues(
            ...SegmentedValues::factory()->fromRaw((new Parser($fileContent))->get())
        );
    }
}
