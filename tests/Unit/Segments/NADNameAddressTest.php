<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\NADNameAddress;
use JsonException;
use PHPUnit\Framework\TestCase;

final class NADNameAddressTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = [
            'NAD',
            'CZ',
            ['0410314', '160', 'Z12'],
            '',
            'Company Returns Centre',
            'c/o Carrier AB',
            'Malmo',
            '',
            '20713',
            'DE',
        ];
        $segment = new NADNameAddress($rawValues);

        self::assertEquals('NAD', $segment->tag());
        self::assertEquals('CZ', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function typed_accessors(): void
    {
        $rawValues = [
            'NAD',
            'CN',
            ['123456', '160', 'Z12'],
            '',
            'Person Name',
            'Street Nr 2',
            'City2',
            '',
            '12345',
            'DE',
        ];
        $segment = new NADNameAddress($rawValues);

        self::assertEquals('CN', $segment->partyQualifier());
        self::assertEquals(['123456', '160', 'Z12'], $segment->partyIdentification());
        self::assertEquals('123456', $segment->partyId());
        self::assertEquals('Person Name', $segment->name());
        self::assertEquals('Street Nr 2', $segment->street());
        self::assertEquals('City2', $segment->city());
        self::assertEquals('12345', $segment->postalCode());
        self::assertEquals('DE', $segment->countryCode());
    }

    /**
     * @test
     */
    public function debug_helpers(): void
    {
        $rawValues = ['NAD', 'CN', [], '', 'Test'];
        $segment = new NADNameAddress($rawValues);

        $array = $segment->toArray();
        self::assertEquals('NAD', $array['tag']);
        self::assertEquals('CN', $array['subId']);
        self::assertEquals($rawValues, $array['rawValues']);

        $json = $segment->toJson(JSON_THROW_ON_ERROR);
        self::assertJson($json);
        self::assertStringContainsString('NAD', $json);
    }

    /**
     * @test
     */
    public function to_json_throws_on_unencodable_data_even_without_the_throw_flag(): void
    {
        $segment = new NADNameAddress(['NAD', 'CN', [], '', "M\xFCller"]);

        $this->expectException(JsonException::class);
        $segment->toJson(JSON_PRETTY_PRINT);
    }
}
