<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Writer;

use EdifactParser\EdifactParser;
use EdifactParser\Segments\BGMBeginningOfMessage;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\Qualifier\NADQualifier;
use EdifactParser\Segments\UNBInterchangeHeader;
use EdifactParser\Segments\UNTMessageFooter;
use EdifactParser\Segments\UNZInterchangeTrailer;
use EdifactParser\Writer\InterchangeBuilder;
use EdifactParser\Writer\MessageBuilder;
use PHPUnit\Framework\TestCase;

final class InterchangeBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function message_builder_exposes_its_reference(): void
    {
        self::assertSame('REF-42', MessageBuilder::create('REF-42', 'ORDERS')->reference());
    }

    /**
     * @test
     */
    public function builds_and_serializes_an_interchange_that_parses_back(): void
    {
        $edi = InterchangeBuilder::create('SENDER', 'RECIPIENT', 'REF1')
            ->preparedAt('200101', '1200')
            ->addMessage(
                MessageBuilder::create('1', 'ORDERS')
                    ->addSegment(new BGMBeginningOfMessage(['BGM', '220']))
                    ->addSegment(NADNameAddress::builder()->withQualifier(NADQualifier::BUYER)->withName('ACME')->build())
            )
            ->toString();

        $result = EdifactParser::createWithDefaultSegments()->parse($edi);

        // One message, correct type
        self::assertCount(1, $result->transactionMessages());
        $message = $result->transactionMessages()[0];
        self::assertSame('ORDERS', $message->messageType());

        // UNT segment count is auto-computed: UNH + BGM + NAD + UNT = 4
        $unt = $message->query()->withTag('UNT')->first();
        self::assertInstanceOf(UNTMessageFooter::class, $unt);
        self::assertSame('4', $unt->segmentCount());

        // UNB metadata survives the round-trip
        $unb = $result->globalSegments()->segmentByTagAndSubId('UNB', 'UNOC');
        self::assertInstanceOf(UNBInterchangeHeader::class, $unb);
        self::assertSame('SENDER', $unb->senderIdentification());
        self::assertSame('RECIPIENT', $unb->recipientIdentification());

        // UNZ interchange control count is auto-computed (1 message)
        $unz = $result->globalSegments()->segmentByTagAndSubId('UNZ', '1');
        self::assertInstanceOf(UNZInterchangeTrailer::class, $unz);
        self::assertSame('1', $unz->interchangeControlCount());
    }

    /**
     * @test
     */
    public function counts_multiple_messages(): void
    {
        $edi = InterchangeBuilder::create('S', 'R', 'REF')
            ->addMessage(MessageBuilder::create('1', 'ORDERS')->addSegment(new BGMBeginningOfMessage(['BGM', '220'])))
            ->addMessage(MessageBuilder::create('2', 'ORDERS')->addSegment(new BGMBeginningOfMessage(['BGM', '221'])))
            ->toString();

        $result = EdifactParser::createWithDefaultSegments()->parse($edi);

        self::assertCount(2, $result->transactionMessages());
        $unz = $result->globalSegments()->segmentByTagAndSubId('UNZ', '2');
        self::assertInstanceOf(UNZInterchangeTrailer::class, $unz);
        self::assertSame('2', $unz->interchangeControlCount());
    }
}
