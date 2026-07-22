<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\EdifactParser;
use EdifactParser\Segments\FTXFreeText;
use EdifactParser\Segments\GIDGoodsItemDetails;
use EdifactParser\Segments\IMDItemDescription;
use EdifactParser\Segments\LOCPlace;
use EdifactParser\Segments\PACPackage;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\TDTTransportDetails;
use PHPUnit\Framework\TestCase;

final class AdditionalSegmentsTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider registeredTags
     *
     * @param class-string $class
     */
    public function factory_creates_typed_segments(string $tag, string $class): void
    {
        $segment = SegmentFactory::withDefaultSegments()->createSegmentFromArray([$tag, 'x']);

        self::assertInstanceOf($class, $segment);
    }

    /**
     * @return array<string, array{0: string, 1: class-string}>
     */
    public static function registeredTags(): array
    {
        return [
            'FTX' => ['FTX', FTXFreeText::class],
            'LOC' => ['LOC', LOCPlace::class],
            'TDT' => ['TDT', TDTTransportDetails::class],
            'IMD' => ['IMD', IMDItemDescription::class],
            'PAC' => ['PAC', PACPackage::class],
            'GID' => ['GID', GIDGoodsItemDetails::class],
        ];
    }

    /**
     * @test
     */
    public function tdt_and_gid_accessors_from_the_sample(): void
    {
        $message = EdifactParser::createWithDefaultSegments()
            ->parseFile(__DIR__ . '/../../../example/edifact-sample.edi')
            ->transactionMessages()[0];

        $tdt = $message->query()->withTag('TDT')->first();
        self::assertInstanceOf(TDTTransportDetails::class, $tdt);
        self::assertSame('20', $tdt->transportStageQualifier());

        $gid = $message->query()->withTag('GID')->first();
        self::assertInstanceOf(GIDGoodsItemDetails::class, $gid);
        self::assertSame('1', $gid->goodsItemNumber());
        self::assertSame('1', $gid->numberOfPackages());
    }

    /**
     * @test
     */
    public function composite_accessors(): void
    {
        $ftx = new FTXFreeText(['FTX', 'AAI', '', '', 'Some free text']);
        self::assertSame('AAI', $ftx->subjectQualifier());
        self::assertSame('Some free text', $ftx->text());

        $loc = new LOCPlace(['LOC', '5', ['DEHAM', '139', '6', 'Hamburg']]);
        self::assertSame('5', $loc->locationQualifier());
        self::assertSame('DEHAM', $loc->locationId());
        self::assertSame('Hamburg', $loc->locationName());

        $pac = new PACPackage(['PAC', '12', '', ['CT']]);
        self::assertSame('12', $pac->numberOfPackages());
        self::assertSame('CT', $pac->packagingTypeCode());

        $imd = new IMDItemDescription(['IMD', 'F', '', ['', '', '', 'Blue shirt']]);
        self::assertSame('F', $imd->descriptionFormatCode());
        self::assertSame('Blue shirt', $imd->itemDescription());
    }
}
