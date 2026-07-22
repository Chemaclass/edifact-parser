<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\EdifactParser;
use EdifactParser\ParserResult;
use EdifactParser\Segments\BGMBeginningOfMessage;
use EdifactParser\Segments\UNBInterchangeHeader;
use EdifactParser\Segments\UNTMessageFooter;
use EdifactParser\Segments\UNZInterchangeTrailer;
use PHPUnit\Framework\TestCase;

final class EnvelopeMetadataTest extends TestCase
{
    /**
     * @test
     */
    public function unb_interchange_header_metadata(): void
    {
        $unb = $this->sample()->globalSegments()->segmentByTagAndSubId('UNB', 'UNOC');

        self::assertInstanceOf(UNBInterchangeHeader::class, $unb);
        self::assertSame('UNOC', $unb->syntaxIdentifier());
        self::assertSame('3', $unb->syntaxVersionNumber());
        self::assertSame('9457386', $unb->senderIdentification());
        self::assertSame('73130012', $unb->recipientIdentification());
        self::assertSame('19101', $unb->preparationDate());
        self::assertSame('118', $unb->preparationTime());
        self::assertSame('8', $unb->interchangeControlReference());
    }

    /**
     * @test
     */
    public function unz_interchange_trailer_metadata(): void
    {
        $unz = $this->sample()->globalSegments()->segmentByTagAndSubId('UNZ', '2');

        self::assertInstanceOf(UNZInterchangeTrailer::class, $unz);
        self::assertSame('2', $unz->interchangeControlCount());
        self::assertSame('8', $unz->interchangeControlReference());
    }

    /**
     * @test
     */
    public function unt_and_bgm_metadata(): void
    {
        $message = $this->sample()->transactionMessages()[0];

        $unt = $message->query()->withTag('UNT')->first();
        self::assertInstanceOf(UNTMessageFooter::class, $unt);
        self::assertSame('18', $unt->segmentCount());
        self::assertSame('1', $unt->messageReferenceNumber());

        $bgm = $message->query()->withTag('BGM')->first();
        self::assertInstanceOf(BGMBeginningOfMessage::class, $bgm);
        self::assertSame('220', $bgm->documentCode());
        self::assertSame('56677786689', $bgm->documentNumber());
        self::assertSame('9', $bgm->messageFunction());
    }
    private function sample(): ParserResult
    {
        return EdifactParser::createWithDefaultSegments()->parseFile(__DIR__ . '/../../example/edifact-sample.edi');
    }
}
