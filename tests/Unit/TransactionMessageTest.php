<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EDI\Parser;
use EdifactParser\LineItem;
use EdifactParser\SegmentList;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Segments\UNBInterchangeHeader;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;
use EdifactParser\TransactionMessage;
use PHPUnit\Framework\TestCase;

final class TransactionMessageTest extends TestCase
{
    /**
     * @test
     */
    public function segments_in_one_message(): void
    {
        $fileContent = "UNH+1+IFTMIN:S:93A:UN:PN001'\nUNT+19+1'";

        self::assertEquals([
            new TransactionMessage([
                'UNH' => [
                    '1' => new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                ],
                'UNT' => [
                    '19' => new UNTMessageFooter(['UNT', '19', '1']),
                ],
            ]),
        ], $this->transactionMessages($fileContent));
    }

    /**
     * @test
     */
    public function segments_in_two_messages(): void
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
                'UNH' => [
                    '1' => new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                ],
                'UNT' => [
                    '19' => new UNTMessageFooter(['UNT', '19', '1']),
                ],
            ]),
            new TransactionMessage([
                'UNH' => [
                    '2' => new UNHMessageHeader(['UNH', '2', ['IFTMIN', 'S', '94A', 'UN', 'PN002']]),
                ],
                'UNT' => [
                    '19' => new UNTMessageFooter(['UNT', '19', '2']),
                ],
            ]),
        ], $this->transactionMessages($fileContent));
    }

    /**
     * @test
     */
    public function one_message_with_multiple_segments_with_the_same_name(): void
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
                'UNH' => [
                    '1' => new UNHMessageHeader(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]),
                ],
                'UNT' => [
                    '19' => new UNTMessageFooter(['UNT', '19', '1']),
                ],
                'CNT' => [
                    '7' => new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
                    '11' => new CNTControl(['CNT', ['11', '1', 'PCE']]),
                    '15' => new CNTControl(['CNT', ['15', '0.068224', 'MTQ']]),
                ],
            ]),
        ], $this->transactionMessages($fileContent));
    }

    /**
     * @test
     */
    public function one_message_is_created_when_start_with_unh_and_ends_with_unt(): void
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
                'UNH' => [
                    '1' => new UNHMessageHeader(['UNH', '1', 'anything']),
                ],
                'CNT' => [
                    '7' => new CNTControl(['CNT', ['7', '0.1', 'KGM']]),
                ],
                'UNT' => [
                    '19' => new UNTMessageFooter(['UNT', '19', '1']),
                ],
            ]),
            new TransactionMessage([
                'UNH' => [
                    '2' => new UNHMessageHeader(['UNH', '2', 'anything']),
                ],
                'UNT' => [
                    '19' => new UNTMessageFooter(['UNT', '19', '2']),
                ],
            ]),
        ], $this->transactionMessages($fileContent));
    }

    /**
     * @test
     */
    public function previous_unh_are_overridden_if_they_doesnt_have_unt(): void
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
                'UNH' => [
                    '2' => new UNHMessageHeader(['UNH', '2', 'anything']),
                ],
                'CNT' => [
                    '5' => new CNTControl(['CNT', ['5', '0.1', 'KGM']]),
                ],
                'UNT' => [
                    '10' => new UNTMessageFooter(['UNT', '10', '2']),
                ],
            ]),
        ], $this->transactionMessages($fileContent));
    }

    /**
     * @test
     */
    public function message_not_created_if_unt_doesnt_have_unh_or_vice_versa(): void
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

    /**
     * @test If there is UNB, it will be present in all messages
     */
    public function message_with_unb(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNB+UNOC:0+1:2+3:4+5+Anything here+6'

UNH+1+anything'
CNT+5:0.1:KGM'
UNT+10+2'

UNH+2+anything'
CNT+6:0.1:KGM'
UNT+11+2'

UNZ+2+3'
EDI;
        $messages = $this->transactionMessages($fileContent);

        $unb = new UNBInterchangeHeader(
            ['UNB', ['UNOC', '0'], ['1', '2'], ['3', '4'], '5', 'Anything here', '6']
        );

        self::assertEquals([
            'UNB' => [
                'UNOC' => $unb,
            ],
            'UNH' => [
                '1' => new UNHMessageHeader(['UNH', '1', 'anything']),
            ],
            'CNT' => [
                '5' => new CNTControl(['CNT', ['5', '0.1', 'KGM']]),
            ],
            'UNT' => [
                '10' => new UNTMessageFooter(['UNT', '10', '2']),
            ],
        ], $messages[0]->allSegments());

        self::assertEquals([
            'UNB' => [
                'UNOC' => $unb,
            ],
            'UNH' => [
                '2' => new UNHMessageHeader(['UNH', '2', 'anything']),
            ],
            'CNT' => [
                '6' => new CNTControl(['CNT', ['6', '0.1', 'KGM']]),
            ],
            'UNT' => [
                '11' => new UNTMessageFooter(['UNT', '11', '2']),
            ],
        ], $messages[1]->allSegments());
    }

    /**
     * @test
     */
    public function all_segments(): void
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
            'UNH' => [
                '1' => new UNHMessageHeader(['UNH', '1', 'anything']),
            ],
            'CNT' => [
                '5' => new CNTControl(['CNT', ['5', '0.1', 'KGM']]),
            ],
            'UNT' => [
                '10' => new UNTMessageFooter(['UNT', '10', '2']),
            ],
        ], $firstMessage->allSegments());
    }

    /**
     * @test
     */
    public function segments_by_tag(): void
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
        ], $firstMessage->segmentsByTag('CNT'));

        self::assertEmpty($firstMessage->segmentsByTag('unknown'));
    }

    /**
     * @test
     */
    public function segment_by_tag_and_sub_id(): void
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
            $firstMessage->segmentByTagAndSubId('CNT', '5')
        );

        self::assertNull($firstMessage->segmentByTagAndSubId('CNT', 'unknown'));
    }

    /**
     * @test
     */
    public function line_items(): void
    {
        $fileContent = <<<EDI
UNA:+.? '
UNH+1+anything'
LIN+1'
QTY+25:5'
LIN+2'
QTY+23:10'
UNS+S'
CNT+5:0.1:KGM'
UNT+10+2'
UNZ+2+3'
EDI;

        $messages = $this->transactionMessages($fileContent);
        $firstMessage = reset($messages);

        $firstLineItem = new LineItem([
            'LIN' => ['1' => new LINLineItem(['LIN', 1])],
            'QTY' => ['25' => new QTYQuantity(['QTY', [25, 5]])],
        ]);

        $secondLineItem = new LineItem([
            'LIN' => ['2' => new LINLineItem(['LIN', 2])],
            'QTY' => ['23' => new QTYQuantity(['QTY', [23, 10]])],
        ]);

        self::assertEquals([
            '1' => $firstLineItem,
            '2' => $secondLineItem,
        ], $firstMessage->lineItems());

        self::assertEquals($firstLineItem, $firstMessage->lineItemById(1));
        self::assertEquals($secondLineItem, $firstMessage->lineItemById(2));
        self::assertNull($firstMessage->lineItemById(3));
    }

    private function transactionMessages(string $fileContent): array
    {
        $segments = SegmentList::withDefaultFactory()
            ->fromRaw((new Parser($fileContent))->get());

        return TransactionMessage::groupSegmentsByMessage(...$segments);
    }
}
