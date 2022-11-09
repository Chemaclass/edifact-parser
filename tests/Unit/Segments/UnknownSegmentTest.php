<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\Segments\UnknownSegment;
use PHPUnit\Framework\TestCase;

final class UnknownSegmentTest extends TestCase
{
    /**
     * @test
     */
    public function segment_values(): void
    {
        $rawValues = ['UNKNOWN', ['some_sub_id', 'data', 'more_data']];
        $segment = new UnknownSegment($rawValues);

        self::assertEquals('UNKNOWN', $segment->tag());
        self::assertEquals('some_sub_id', $segment->subId());
        self::assertEquals($rawValues, $segment->rawValues());
    }

    /**
     * @test
     */
    public function tag_always_returns_first_value(): void
    {
        self::assertEquals(
            'TAG',
            (new UnknownSegment(['TAG', 'sub_id']))->tag()
        );
        self::assertEquals(
            'other_tag',
            (new UnknownSegment(['other_tag', 'sub_id']))->tag()
        );
        self::assertEquals(
            'THETAG',
            (new UnknownSegment(['THETAG', 'sub_id']))->tag()
        );
    }

    /**
     * @test
     */
    public function sub_id_infers_sub_id_if_present(): void
    {
        self::assertEquals(
            'other_sub_id',
            (new UnknownSegment(['unknown', 'other_sub_id']))->subId()
        );

        self::assertEquals(
            'yet_another_sub_id',
            (new UnknownSegment(['unknown', ['yet_another_sub_id']]))->subId()
        );
    }

    /**
     * @test
     */
    public function sub_id_returns_hash_if_no_sub_id_present(): void
    {
        $rawValues = ['unknown', [['other_sub_id']]];
        self::assertEquals(
            md5(json_encode($rawValues)),
            (new UnknownSegment($rawValues))->subId()
        );
    }
}
