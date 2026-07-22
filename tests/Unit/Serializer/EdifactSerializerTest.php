<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Serializer;

use EDI\Parser;
use EdifactParser\SegmentList;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\UnknownSegment;
use EdifactParser\Serializer\EdifactSerializer;
use EdifactParser\Serializer\UnaSeparators;
use PHPUnit\Framework\TestCase;

final class EdifactSerializerTest extends TestCase
{
    /**
     * @test
     */
    public function serializes_a_segment_with_a_composite_element(): void
    {
        $segment = new UnknownSegment(['UNH', '1', ['IFTMIN', 'S', '93A', 'UN', 'PN001']]);

        self::assertSame(
            "UNH+1+IFTMIN:S:93A:UN:PN001'",
            (new EdifactSerializer())->serializeSegment($segment),
        );
    }

    /**
     * @test
     */
    public function escapes_separators_and_the_release_char_inside_values(): void
    {
        // '+46980100' must become '?+46980100'; a literal '?' must double.
        $segment = new UnknownSegment(['COM', ['+46980100', 'AL']]);

        self::assertSame(
            "COM+?+46980100:AL'",
            (new EdifactSerializer())->serializeSegment($segment),
        );

        self::assertSame(
            "FTX+a??b:c?'d'",
            (new EdifactSerializer())->serializeSegment(new UnknownSegment(['FTX', ['a?b', "c'd"]])),
        );
    }

    /**
     * @test
     */
    public function serialize_joins_segments_and_can_prepend_the_una(): void
    {
        $serializer = new EdifactSerializer();
        $segments = [
            new UnknownSegment(['UNH', '1', ['ORDERS']]),
            new UnknownSegment(['BGM', '220']),
        ];

        self::assertSame("UNH+1+ORDERS'\nBGM+220'", $serializer->serialize($segments));
        self::assertSame("UNA:+.? '\nUNH+1+ORDERS'\nBGM+220'", $serializer->serialize($segments, includeUna: true));
    }

    /**
     * @test
     */
    public function honours_custom_separators(): void
    {
        $serializer = new EdifactSerializer(new UnaSeparators(component: '#', element: '|', segmentTerminator: '~'));

        self::assertSame(
            'NAD|CN|a#b~',
            $serializer->serializeSegment(new UnknownSegment(['NAD', 'CN', ['a', 'b']])),
        );
    }

    /**
     * @test
     */
    public function round_trips_the_sample_file_through_the_low_level_parser(): void
    {
        $original = (new Parser())->loadString((string) file_get_contents(__DIR__ . '/../../../example/edifact-sample.edi'))->get();

        $segments = (new SegmentList(SegmentFactory::withDefaultSegments()))->fromRaw($original);
        $edi = (new EdifactSerializer())->serialize($segments);

        $reparsed = (new Parser())->loadString($edi)->get();

        self::assertSame($original, $reparsed);
    }
}
