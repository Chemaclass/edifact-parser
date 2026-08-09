<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use EdifactParser\EdifactParser;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Segments\SegmentDescriptor;
use EdifactParser\Segments\SegmentFactory;
use PHPUnit\Framework\TestCase;

final class SegmentIntrospectionTest extends TestCase
{
    /**
     * @test
     */
    public function the_factory_lists_the_tags_it_can_build(): void
    {
        $tags = SegmentFactory::withDefaultSegments()->registeredTags();

        self::assertCount(32, $tags);
        self::assertContains('NAD', $tags);
        self::assertContains('UNH', $tags);
        self::assertNotContains('ZZZ', $tags);

        $sorted = $tags;
        sort($sorted);
        self::assertSame($sorted, $tags);
    }

    /**
     * @test
     */
    public function the_factory_resolves_a_tag_to_its_class(): void
    {
        $factory = SegmentFactory::withDefaultSegments();

        self::assertSame(NADNameAddress::class, $factory->classForTag('NAD'));
        self::assertNull($factory->classForTag('ZZZ'));
    }

    /**
     * @test
     */
    public function a_segment_describes_its_own_accessors(): void
    {
        $descriptor = SegmentFactory::withDefaultSegments()->describeTag('QTY');

        self::assertInstanceOf(SegmentDescriptor::class, $descriptor);
        self::assertSame('QTY', $descriptor->tag());
        self::assertSame(QTYQuantity::class, $descriptor->className());
        self::assertSame([
            'measureUnit' => 'string',
            'qualifier' => 'string',
            'quantity' => 'string',
            'quantityAsFloat' => 'float',
        ], $descriptor->accessors());
    }

    /**
     * @test
     */
    public function structural_methods_are_not_reported_as_fields(): void
    {
        $accessors = SegmentDescriptor::forClass(NADNameAddress::class)->accessors();

        foreach (['tag', 'subId', 'parsedSubId', 'rawValues', 'toArray', 'toJson', 'builder'] as $structural) {
            self::assertArrayNotHasKey($structural, $accessors);
        }

        self::assertArrayHasKey('countryCode', $accessors);
    }

    /**
     * @test
     */
    public function methods_taking_arguments_are_behaviour_not_fields(): void
    {
        $accessors = SegmentDescriptor::forClass(EQDFakeSegment::class)->accessors();

        self::assertSame([
            'equipmentTypeCode' => 'string',
            // An optional-argument reader is still a field; a required one is not.
            'sizeTypeCode' => 'string',
        ], $accessors);
    }

    /**
     * @test
     */
    public function custom_segments_are_introspectable_too(): void
    {
        $factory = SegmentFactory::withAdditionalSegments(['EQD' => EQDFakeSegment::class]);

        self::assertContains('EQD', $factory->registeredTags());
        self::assertSame('EQD', $factory->describeTag('EQD')?->tag());
    }

    /**
     * @test
     */
    public function an_unregistered_tag_has_no_descriptor(): void
    {
        self::assertNull(SegmentFactory::withDefaultSegments()->describeTag('ZZZ'));
    }

    /**
     * @test
     */
    public function a_descriptor_serialises(): void
    {
        $data = SegmentDescriptor::forClass(QTYQuantity::class)->toArray();

        self::assertSame('QTY', $data['tag']);
        self::assertSame(QTYQuantity::class, $data['class']);
        self::assertArrayHasKey('quantityAsFloat', $data['accessors']);
    }

    /**
     * @test
     */
    public function every_registered_tag_can_be_described(): void
    {
        $factory = SegmentFactory::withDefaultSegments();

        foreach ($factory->registeredTags() as $tag) {
            $descriptor = $factory->describeTag($tag);

            self::assertNotNull($descriptor, "no descriptor for {$tag}");
            self::assertSame($tag, $descriptor->tag(), "descriptor tag mismatch for {$tag}");
        }
    }

    /**
     * @test
     */
    public function the_committed_schema_matches_what_a_message_actually_produces(): void
    {
        $schema = json_decode(
            (string) file_get_contents(__DIR__ . '/../../../schema/message.schema.json'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $message = EdifactParser::createWithDefaultSegments()
            ->parse("UNH+1+ORDERS:D:96A:UN'NAD+BY+++ACME'UNT+3+1'")
            ->transactionMessages()[0];

        $data = $message->toArray();

        // The schema is only useful if it describes the real output, so assert the
        // contract rather than trusting the document.
        self::assertSame(['type', 'segments'], $schema['required']);
        self::assertSame(array_keys($data), $schema['required']);

        $segmentProperties = $schema['$defs']['segment']['properties'];
        foreach ($data['segments'] as $segment) {
            foreach (array_keys($segment) as $key) {
                self::assertArrayHasKey($key, $segmentProperties, "schema is missing '{$key}'");
            }
        }

        foreach ($schema['$defs']['segment']['required'] as $required) {
            self::assertArrayHasKey($required, $data['segments'][0]);
        }
    }
}
