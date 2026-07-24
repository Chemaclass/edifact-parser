<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\EdifactParser;
use EdifactParser\Segments\COMCommunicationContact;
use EdifactParser\Segments\CTAContactInformation;
use EdifactParser\Segments\FTXFreeText;
use EdifactParser\Segments\GIDGoodsItemDetails;
use EdifactParser\Segments\IMDItemDescription;
use EdifactParser\Segments\LOCPlace;
use EdifactParser\Segments\NullSegment;
use EdifactParser\Segments\PACPackage;
use EdifactParser\Segments\PATPaymentTerms;
use EdifactParser\Segments\PCDPercentageDetails;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\TAXDutyTaxFee;
use EdifactParser\Segments\TDTTransportDetails;
use EdifactParser\Segments\TODTermsOfDelivery;
use EdifactParser\Segments\UNEFunctionalGroupTrailer;
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
            'CTA' => ['CTA', CTAContactInformation::class],
            'COM' => ['COM', COMCommunicationContact::class],
            'TAX' => ['TAX', TAXDutyTaxFee::class],
            'PCD' => ['PCD', PCDPercentageDetails::class],
            'PAT' => ['PAT', PATPaymentTerms::class],
            'TOD' => ['TOD', TODTermsOfDelivery::class],
        ];
    }

    /**
     * @test
     */
    public function party_and_terms_accessors(): void
    {
        $cta = new CTAContactInformation(['CTA', 'IC', ['00001', 'John Smith']]);
        self::assertSame('IC', $cta->contactFunction());
        self::assertSame('00001', $cta->contactId());
        self::assertSame('John Smith', $cta->contactName());

        $com = new COMCommunicationContact(['COM', ['john@acme.com', 'EM']]);
        self::assertSame('john@acme.com', $com->communicationNumber());
        self::assertSame('EM', $com->channelQualifier());

        $tax = new TAXDutyTaxFee(['TAX', '7', ['VAT'], '', '', ['', '', '', '19'], 'S']);
        self::assertSame('7', $tax->functionQualifier());
        self::assertSame('VAT', $tax->typeCode());
        self::assertSame('19', $tax->rate());
        self::assertSame('S', $tax->categoryCode());

        $pcd = new PCDPercentageDetails(['PCD', ['1', '10']]);
        self::assertSame('1', $pcd->percentageQualifier());
        self::assertSame('10', $pcd->percentage());

        $pat = new PATPaymentTerms(['PAT', '1', ['3']]);
        self::assertSame('1', $pat->typeQualifier());
        self::assertSame('3', $pat->termsId());

        $tod = new TODTermsOfDelivery(['TOD', '6', '', ['CIF', '', '', 'Cost Insurance Freight']]);
        self::assertSame('6', $tod->functionCode());
        self::assertSame('', $tod->transportChargesMethod());
        self::assertSame('CIF', $tod->termsCode());
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
    public function detail_segment_accessors(): void
    {
        $tdt = new TDTTransportDetails(['TDT', '20', 'VOY-1', ['30'], '', ['CARRIER-ID']]);
        self::assertSame('20', $tdt->transportStageQualifier());
        self::assertSame('VOY-1', $tdt->conveyanceReference());
        self::assertSame('30', $tdt->modeOfTransport());
        self::assertSame('CARRIER-ID', $tdt->carrierId());

        $gid = new GIDGoodsItemDetails(['GID', '1', ['5', 'CT']]);
        self::assertSame('1', $gid->goodsItemNumber());
        self::assertSame('5', $gid->numberOfPackages());
        self::assertSame('CT', $gid->packageTypeCode());

        $imd = new IMDItemDescription(['IMD', 'C', '35', ['BLUE', '', '', 'Blue']]);
        self::assertSame('C', $imd->descriptionFormatCode());
        self::assertSame('35', $imd->itemCharacteristicCode());
        self::assertSame('BLUE', $imd->itemDescriptionCode());
        self::assertSame('Blue', $imd->itemDescription());
    }

    /**
     * @test
     *
     * @dataProvider tagOnlyExpectations
     *
     * @param class-string $class
     */
    public function every_default_segment_reports_its_tag(string $tag, string $class): void
    {
        /** @var \EdifactParser\Segments\SegmentInterface $segment */
        $segment = new $class([$tag]);

        self::assertSame($tag, $segment->tag());
    }

    /**
     * @return array<string, array{0: string, 1: class-string}>
     */
    public static function tagOnlyExpectations(): array
    {
        return [
            'FTX' => ['FTX', FTXFreeText::class],
            'LOC' => ['LOC', LOCPlace::class],
            'PAC' => ['PAC', PACPackage::class],
            'PAT' => ['PAT', PATPaymentTerms::class],
            'PCD' => ['PCD', PCDPercentageDetails::class],
            'TAX' => ['TAX', TAXDutyTaxFee::class],
            'TOD' => ['TOD', TODTermsOfDelivery::class],
            'IMD' => ['IMD', IMDItemDescription::class],
        ];
    }

    /**
     * @test
     */
    public function une_group_reference(): void
    {
        $une = new UNEFunctionalGroupTrailer(['UNE', '3', 'GRP-1']);

        self::assertSame('3', $une->controlCount());
        self::assertSame('GRP-1', $une->groupReference());
    }

    /**
     * @test
     */
    public function null_segment_has_an_empty_tag(): void
    {
        self::assertSame('', (new NullSegment())->tag());
    }

    /**
     * @test
     */
    public function parsed_sub_id_splits_a_plain_string_element(): void
    {
        // rawValues[1] is a plain string, so parsedSubId splits it on ':'.
        $segment = new FTXFreeText(['FTX', 'AAI:SUB']);

        self::assertSame(['AAI', 'SUB'], $segment->parsedSubId());
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
