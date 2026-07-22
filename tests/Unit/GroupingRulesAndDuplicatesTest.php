<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\EdifactParser;
use EdifactParser\GroupingRules;
use EdifactParser\Segments\SegmentFactory;
use PHPUnit\Framework\TestCase;

final class GroupingRulesAndDuplicatesTest extends TestCase
{
    /**
     * @test
     */
    public function query_preserves_duplicate_segments_with_the_same_tag_and_sub_id(): void
    {
        // Two RFF segments share tag+subId ('CU'); the keyed view keeps one, but
        // query() and segments() must retain both.
        $edi = "UNH+1+ORDERS:D:96A:UN'RFF+CU:A'RFF+CU:B'UNT+4+1'";

        $message = EdifactParser::createWithDefaultSegments()->parse($edi)->transactionMessages()[0];

        self::assertCount(2, $message->query()->withTag('RFF')->get());
        self::assertCount(4, $message->segments());
        self::assertSame(4, $message->count());
        // The keyed single lookup still resolves (to the last one).
        self::assertNotNull($message->segmentByTagAndSubId('RFF', 'CU'));
    }

    /**
     * @test
     */
    public function custom_grouping_rules_change_context_detection(): void
    {
        $edi = "UNH+1+ORDERS:D:96A:UN'NAD+BY'CTA+IC'UNT+4+1'";

        $default = EdifactParser::createWithDefaultSegments()->parse($edi)->transactionMessages()[0];
        self::assertCount(1, $default->contextSegments());

        // Remove NAD as a context parent: no context hierarchy is built.
        $rules = GroupingRules::default()->withContextTags([]);
        $custom = new EdifactParser(SegmentFactory::withDefaultSegments(), $rules);

        $customMessage = $custom->parse($edi)->transactionMessages()[0];
        self::assertCount(0, $customMessage->contextSegments());
    }
}
