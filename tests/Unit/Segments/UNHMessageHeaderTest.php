<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Exception\MissingSubId;
use EdifactParser\Segments\UNHMessageHeader;
use PHPUnit\Framework\TestCase;

final class UNHMessageHeaderTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']];
        $segment = new UNHMessageHeader($rawValues);

        self::assertEquals('UNH', $segment->tag());
        self::assertEquals('1', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function missing_sub_id(): void
    {
        $segment = new UNHMessageHeader(['UNH']);
        $this->expectException(MissingSubId::class);
        $segment->subId();
    }

    /**
     * @test
     */
    public function message_type_detection(): void
    {
        $rawValues = ['UNH', '1', ['ORDERS', 'D', '96A', 'UN']];
        $segment = new UNHMessageHeader($rawValues);

        self::assertEquals('1', $segment->messageReferenceNumber());
        self::assertEquals(['ORDERS', 'D', '96A', 'UN'], $segment->messageIdentifier());
        self::assertEquals('ORDERS', $segment->messageType());
        self::assertEquals('D', $segment->messageVersionNumber());
        self::assertEquals('96A', $segment->messageReleaseNumber());
        self::assertEquals('UN', $segment->controllingAgency());
    }

    /**
     * @test
     */
    public function different_message_types(): void
    {
        $types = [
            'ORDERS' => ['UNH', '1', ['ORDERS', 'D', '96A', 'UN']],
            'INVOIC' => ['UNH', '2', ['INVOIC', 'D', '01B', 'UN']],
            'DESADV' => ['UNH', '3', ['DESADV', 'D', '96A', 'UN']],
            'IFTMIN' => ['UNH', '4', ['IFTMIN', 'S', '93A', 'UN']],
        ];

        foreach ($types as $expectedType => $rawValues) {
            $segment = new UNHMessageHeader($rawValues);
            self::assertEquals($expectedType, $segment->messageType());
        }
    }
}
