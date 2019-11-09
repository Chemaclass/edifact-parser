<?php

declare(strict_types=1);

namespace App\Tests\EdifactParser;

use App\EdifactParser\Segments\CNTControl;
use App\EdifactParser\Segments\MEADimensions;
use App\EdifactParser\Segments\UNHMessageHeader;
use App\EdifactParser\Segments\UNTMessageFooter;
use App\EdifactParser\TransactionMessage;
use PHPUnit\Framework\TestCase;

final class TransactionMessageTest extends TestCase
{
    /** @test */
    public function twoSegmentsWithDifferentNames(): void
    {
        $message = new TransactionMessage([
            new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
            new MEADimensions(['MEA', 'WT', 'G', ['KGM', '0.1']]),
        ]);

        self::assertEquals([
            CNTControl::NAME => [
                '7' => new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
            ],
            MEADimensions::NAME => [
                'WT' => new MEADimensions(['MEA', 'WT', 'G', ['KGM', '0.1']]),
            ],
        ], $message->segments());
    }

    /** @test */
    public function twoSegmentsWithTheSameName(): void
    {
        $message = new TransactionMessage([
            new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
            new UNTMessageFooter(['UNT', '19', '1']),
            new MEADimensions(['MEA', 'WT', 'G', ['KGM', '0.1']]),
            new MEADimensions(['MEA', 'VOL', '', ['MTQ', '0.06822']]),
        ]);

        self::assertEquals([
            UNHMessageHeader::NAME => [
                '1' => new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
            ],
            UNTMessageFooter::NAME => [
                '19' => new UNTMessageFooter(['UNT', '19', '1']),
            ],
            MEADimensions::NAME => [
                'WT' => new MEADimensions(['MEA', 'WT', 'G', ['KGM', '0.1']]),
                'VOL' => new MEADimensions(['MEA', 'VOL', '', ['MTQ', '0.06822']]),
            ],
        ], $message->segments());
    }

    /** @test */
    public function moreThanTwoSegmentsWithTheSameName(): void
    {
        $message = new TransactionMessage([
            new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
            new UNTMessageFooter(['UNT', '19', '1']),
            new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
            new CNTControl(['CNT', ['11', '1', 'PCE']]),
            new CNTControl(['CNT', ['15', '0.068224', 'MTQ']]),
        ]);

        self::assertEquals([
            UNHMessageHeader::NAME => [
                '1' => new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
            ],
            UNTMessageFooter::NAME => [
                '19' => new UNTMessageFooter(['UNT', '19', '1']),
            ],
            CNTControl::NAME => [
                '7' => new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
                '11' => new CNTControl(['CNT', ['11', '1', 'PCE']]),
                '15' => new CNTControl(['CNT', ['15', '0.068224', 'MTQ']]),
            ],
        ], $message->segments());
    }
}
