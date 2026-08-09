<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\EdifactParser;
use EdifactParser\Segments\AbstractSegment;
use EdifactParser\Segments\GeneratedSegments;
use EdifactParser\Segments\SegmentDescriptor;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UnknownSegment;
use PHPUnit\Framework\TestCase;

use function count;

/**
 * The generated classes are mechanical, so testing them one by one would be theatre. What
 * matters is that every one of them is loadable, tagged correctly, and that every accessor
 * reads without blowing up — which this exercises exhaustively.
 */
final class GeneratedSegmentsTest extends TestCase
{
    /**
     * @test
     */
    public function the_defaults_keep_their_size_and_meaning(): void
    {
        // Generating a directory must not quietly change what "default" means.
        self::assertCount(32, SegmentFactory::DEFAULT_SEGMENTS);
        self::assertSame(
            [],
            array_intersect_key(SegmentFactory::DEFAULT_SEGMENTS, GeneratedSegments::SEGMENTS),
            'a generated class must never shadow a hand-written one',
        );
    }

    /**
     * @test
     */
    public function the_directory_factory_adds_them_on_top_of_the_defaults(): void
    {
        $factory = SegmentFactory::withDirectorySegments();
        $tags = $factory->registeredTags();

        self::assertCount(32 + count(GeneratedSegments::SEGMENTS), $tags);
        self::assertContains('NAD', $tags);
        self::assertContains('EQD', $tags);

        // Hand-written classes still win for the tags they cover.
        self::assertSame(
            SegmentFactory::DEFAULT_SEGMENTS['NAD'],
            $factory->classForTag('NAD'),
        );
    }

    /**
     * @test
     */
    public function every_generated_class_is_a_segment_with_the_right_tag(): void
    {
        // withDirectorySegments() skips per-class validation to stay lazy, so this is what
        // guards the invariant instead.
        foreach (GeneratedSegments::SEGMENTS as $tag => $className) {
            self::assertTrue(class_exists($className), "{$className} does not exist");

            $segment = new $className([]);
            self::assertInstanceOf(AbstractSegment::class, $segment);
            self::assertInstanceOf(SegmentInterface::class, $segment);
            self::assertSame($tag, $segment->tag(), "{$className} reports the wrong tag");
        }
    }

    /**
     * @test
     */
    public function every_generated_accessor_reads_without_error(): void
    {
        $rawValues = self::sampleRawValues();
        $accessorCount = 0;

        foreach (GeneratedSegments::SEGMENTS as $className) {
            $segment = new $className($rawValues);

            foreach (array_keys(SegmentDescriptor::forClass($className)->accessors()) as $accessor) {
                // Every generated accessor is a no-argument string reader over rawValues;
                // running them all against a densely populated segment proves none of them
                // reads out of bounds or returns the wrong type.
                self::assertIsString($segment->{$accessor}(), "{$className}::{$accessor}()");
                ++$accessorCount;
            }
        }

        // A sanity floor, so an empty generation run cannot make this test vacuous.
        self::assertGreaterThan(500, $accessorCount);
    }

    /**
     * @test
     */
    public function accessors_never_shadow_the_base_class(): void
    {
        $reserved = ['tag', 'subId', 'parsedSubId', 'rawValues', 'toArray', 'toJson'];

        foreach (GeneratedSegments::SEGMENTS as $className) {
            $accessors = array_keys(SegmentDescriptor::forClass($className)->accessors());

            foreach ($reserved as $name) {
                self::assertNotContains($name, $accessors, "{$className} shadows {$name}()");
            }
        }
    }

    /**
     * @test
     */
    public function accessors_read_the_positions_the_directory_declares(): void
    {
        // EQD: 8053 is the simple element 1, C237/8260 is component 0 of element 2.
        $eqd = new \EdifactParser\Segments\Generated\EQDEquipmentDetails(
            ['EQD', 'CN', ['ABCU1234567', '', '', 'x'], ['4510', '102', '5']],
        );

        self::assertSame('EQD', $eqd->tag());
        self::assertSame('CN', $eqd->equipmentQualifier());
        self::assertSame('ABCU1234567', $eqd->equipmentIdentificationNumber());
    }

    /**
     * @test
     */
    public function a_directory_tag_parses_into_its_generated_class(): void
    {
        $parser = new EdifactParser(SegmentFactory::withDirectorySegments());

        $message = $parser
            ->parse("UNH+1+IFTMIN:S:93A:UN'EQD+CN+ABCU1234567+4510:102:5'UNT+3+1'")
            ->transactionMessages()[0];

        $eqd = $message->segmentByTagAndSubId('EQD', 'CN');

        self::assertInstanceOf(\EdifactParser\Segments\Generated\EQDEquipmentDetails::class, $eqd);
        self::assertSame('ABCU1234567', $eqd->equipmentIdentificationNumber());
    }

    /**
     * @test
     */
    public function the_default_factory_still_treats_directory_tags_as_unknown(): void
    {
        // Opt-in means opt-in: the default factory must be unchanged.
        self::assertInstanceOf(
            UnknownSegment::class,
            SegmentFactory::withDefaultSegments()->createSegmentFromArray(['EQD', 'CN']),
        );
    }

    /**
     * A densely populated segment: every element present, both as a simple value and as a
     * composite with several components, so any accessor position resolves to something.
     *
     * @return array<int, string|list<string>>
     */
    private static function sampleRawValues(): array
    {
        $rawValues = ['TAG'];

        for ($element = 1; $element <= 40; ++$element) {
            $rawValues[] = ['c0', 'c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7', 'c8', 'c9'];
        }

        return $rawValues;
    }
}
