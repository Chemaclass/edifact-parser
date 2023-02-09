<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Exception\MissingSubId;
use EdifactParser\Segments\UNBInterchangeHeader;
use PHPUnit\Framework\TestCase;

final class UNBInterchangeHeaderTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = [
            'UNB',
            ['UNOC', '3'],
            ['9457386', '30'],
            ['73130012', '30'],
            ['19101', '118'],
            '8',
            'MPM 2.19',
            '1424',
        ];
        $segment = new UNBInterchangeHeader($rawValues);

        self::assertEquals('UNB', $segment->tag());
        self::assertEquals('UNOC', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function missing_sub_id(): void
    {
        $segment = new UNBInterchangeHeader(['UNB']);
        $this->expectException(MissingSubId::class);
        $segment->subId();
    }
}
