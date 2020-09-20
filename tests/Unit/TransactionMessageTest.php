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

    /** @test */
    public function oneMessageIsCreatedWhenStartWithUNHAndEndsWithUNT(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+anything'
CNT+7:0.1:KGM'
UNT+19+1'
IGN+ORE:ME'
UNH+2+anything'
UNT+19+2'
UNZ+2+3'
EDI;
        self::assertEquals([
            new TransactionMessage([
                UNHMessageHeader::class => [
                    '1' => new UNHMessageHeader(['UNH', '1', 'anything']),
                ],
                CNTControl::class => [
                    '7' => new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
                ],
                UNTMessageFooter::class => [
                    '19' => new UNTMessageFooter(['UNT', '19', '1']),
                ],
            ]),
            new TransactionMessage([
                UNHMessageHeader::class => [
                    '2' => new UNHMessageHeader(['UNH', '2', 'anything']),
                ],
                UNTMessageFooter::class => [
                    '19' => new UNTMessageFooter(['UNT', '19', '2']),
                ],
            ]),
        ], $this->transactionMessages($fileContent));
    }

    /** @test */
    public function previousUNHAreOverriddenIfTheyDoesntHaveUNT(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+anything'
UNH+2+anything'
CNT+5:0.1:KGM'
UNT+10+2'
UNH+3+anything'
UNZ+2+3'
EDI;
        self::assertEquals([
            new TransactionMessage([
                UNHMessageHeader::class => [
                    '2' => new UNHMessageHeader(['UNH', '2', 'anything']),
                ],
                CNTControl::class => [
                    '5' => new CNTControl(['CNT', ['5', '0.1', 'KGM']]),
                ],
                UNTMessageFooter::class => [
                    '10' => new UNTMessageFooter(['UNT', '10', '2']),
                ],
            ]),
        ], $this->transactionMessages($fileContent));
    }

    /** @test */
    public function messageNotCreatedIfUNTDoesntHaveUNHOrViceVersa(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
CNT+1000:0.1:KGM'
UNT+10+2'
CNT+2000:0.2:KGM'
UNH+3+anything'
CNT+3000:0.3:KGM'
UNZ+2+3'
EDI;
        self::assertEquals([], $this->transactionMessages($fileContent));
    }

    /** @test */
    public function segmentsByTag(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+anything'
CNT+5:0.1:KGM'
UNT+10+2'
UNZ+2+3'
EDI;
        $messages = $this->transactionMessages($fileContent);
        /** @var TransactionMessage $firstMessage */
        $firstMessage = reset($messages);

        self::assertEquals([
            '5' => new CNTControl(['CNT', ['5', '0.1', 'KGM']]),
        ], $firstMessage->segmentsByTag(CNTControl::class));

        self::assertEmpty($firstMessage->segmentsByTag('unknown'));
    }

    /** @test */
    public function segmentByTagAndSubId(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+anything'
CNT+5:0.1:KGM'
UNT+10+2'
UNZ+2+3'
EDI;
        $messages = $this->transactionMessages($fileContent);
        /** @var TransactionMessage $firstMessage */
        $firstMessage = reset($messages);

        self::assertEquals(
            new CNTControl(['CNT', ['5', '0.1', 'KGM']]),
            $firstMessage->segmentByTagAndSubId(CNTControl::class, '5')
        );

        self::assertNull($firstMessage->segmentByTagAndSubId(CNTControl::class, 'unknown'));
    }

    private function transactionMessages(string $fileContent): array
    {
        $segments = SegmentList::withDefaultFactory()
            ->fromRaw((new Parser($fileContent))->get());

        return TransactionMessage::groupSegmentsByMessage(...$segments);
    }
}
