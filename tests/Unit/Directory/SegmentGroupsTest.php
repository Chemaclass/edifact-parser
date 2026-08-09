<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Directory;

use EdifactParser\Directory\GroupInstance;
use EdifactParser\Directory\MessageStructure;
use EdifactParser\Directory\SegmentGroup;
use EdifactParser\Directory\SegmentPosition;
use EdifactParser\Directory\StructureGrouper;
use EdifactParser\Directory\XmlDirectory;
use EdifactParser\EdifactParser;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\TransactionMessage;
use PHPUnit\Framework\TestCase;

/**
 * Segment groups are what GroupingRules only approximates. The fixture message mirrors the
 * shape of a real one — a repeating group, a mandatory group, and a nested group — without
 * needing the 150 MB directory package; the last test uses the real ORDERS D96A.
 */
final class SegmentGroupsTest extends TestCase
{
    private const FIXTURES = __DIR__ . '/../../fixtures/directory';

    /**
     * @test
     */
    public function reads_nested_groups_with_their_occurrence_limits(): void
    {
        $structure = self::structure();

        self::assertSame('TESTMSG', $structure->messageType());
        self::assertSame(3, $structure->groupCount());

        $sg2 = $structure->group('SG2');
        self::assertInstanceOf(SegmentGroup::class, $sg2);
        self::assertSame('NAD', $sg2->triggerTag());
        self::assertSame(2, $sg2->maxRepeat());
        self::assertTrue($sg2->isRequired());

        $sg1 = $structure->group('SG1');
        self::assertNotNull($sg1);
        self::assertSame(3, $sg1->maxRepeat());
        self::assertFalse($sg1->isRequired());

        // SG3 is nested inside SG2 but still reachable by id.
        self::assertSame('CTA', $structure->group('SG3')?->triggerTag());
        self::assertNull($structure->group('SG9'));

        $trigger = $sg1->parts()[0];
        self::assertInstanceOf(SegmentPosition::class, $trigger);
        self::assertSame('RFF', $trigger->tag());
        self::assertTrue($trigger->isRequired());
        self::assertSame(1, $trigger->maxRepeat());
    }

    /**
     * @test
     */
    public function groups_segments_into_the_declared_structure(): void
    {
        $message = self::parse(
            "UNH+1+TESTMSG'BGM+220'DTM+137:20240101:102'"
            . "RFF+ON:1'DTM+171:20231201:102'"
            . "NAD+BY'CTA+IC'COM+a@b.c:EM'"
            . "NAD+SU'"
            . "UNT+9+1'",
        );

        self::assertSame([
            'UNH',
            'BGM',
            'DTM',
            'SG1#0 [RFF,DTM]',
            'SG2#0 [NAD]',
            '  SG3#0 [CTA,COM]',
            'SG2#1 [NAD]',
            'UNT',
        ], self::outline((new StructureGrouper())->group($message, self::structure())));
    }

    /**
     * @test
     */
    public function a_group_repeats_up_to_its_limit(): void
    {
        $message = self::parse("UNH+1+TESTMSG'BGM+220'RFF+A'RFF+B'RFF+C'NAD+BY'UNT+7+1'");

        $nodes = (new StructureGrouper())->group($message, self::structure());
        $groups = array_values(array_filter($nodes, static fn ($n) => $n instanceof GroupInstance && $n->id() === 'SG1'));

        self::assertCount(3, $groups);
        self::assertSame([0, 1, 2], array_map(static fn (GroupInstance $g) => $g->occurrence(), $groups));
    }

    /**
     * @test
     */
    public function segments_beyond_a_repeat_limit_are_still_returned(): void
    {
        // SG1 allows 3 repeats; a fourth RFF cannot be grouped, but dropping it silently
        // would be worse than grouping imperfectly.
        $message = self::parse("UNH+1+TESTMSG'BGM+220'RFF+A'RFF+B'RFF+C'RFF+D'UNT+7+1'");

        $tags = array_map(
            static fn ($n) => $n instanceof GroupInstance ? $n->id() : $n->tag(),
            (new StructureGrouper())->group($message, self::structure()),
        );

        self::assertContains('RFF', $tags, 'the ungrouped RFF must survive');
        self::assertSame(4, substr_count(implode(' ', $tags), 'SG1') + 1);
    }

    /**
     * @test
     */
    public function a_group_instance_exposes_its_contents(): void
    {
        $message = self::parse("UNH+1+TESTMSG'BGM+220'NAD+BY'CTA+IC'COM+a@b.c:EM'UNT+6+1'");

        $nodes = (new StructureGrouper())->group($message, self::structure());
        $sg2 = null;

        foreach ($nodes as $node) {
            if ($node instanceof GroupInstance && $node->id() === 'SG2') {
                $sg2 = $node;
            }
        }

        self::assertInstanceOf(GroupInstance::class, $sg2);
        self::assertSame('SG2', $sg2->id());
        self::assertSame(0, $sg2->occurrence());
        self::assertCount(1, $sg2);
        self::assertSame('NAD', $sg2->segmentByTag('NAD')?->tag());
        self::assertNull($sg2->segmentByTag('ZZZ'));
        self::assertCount(1, $sg2->childrenOfGroup('SG3'));
        self::assertSame([], $sg2->childrenOfGroup('SG9'));

        foreach ($sg2 as $segment) {
            self::assertSame('NAD', $segment->tag());
        }

        $asArray = $sg2->toArray();
        self::assertSame('SG2', $asArray['group']);
        self::assertSame(0, $asArray['occurrence']);
        self::assertSame('NAD', $asArray['segments'][0]['tag']);
        self::assertSame('SG3', $asArray['children'][0]['group']);
    }

    /**
     * @test
     */
    public function an_unknown_message_type_has_no_structure(): void
    {
        self::assertNull(XmlDirectory::fromPath('TEST', self::FIXTURES . '/TEST')->messageStructure('NOPE'));
    }

    /**
     * @test
     */
    public function a_structure_with_no_groups_returns_the_segments_unchanged(): void
    {
        $structure = new MessageStructure('FLAT', [
            new SegmentPosition('UNH', true, 1),
            new SegmentPosition('BGM', true, 1),
        ]);

        $message = self::parse("UNH+1+FLAT'BGM+220'UNT+3+1'");
        $nodes = (new StructureGrouper())->group($message, $structure);

        self::assertSame(['UNH', 'BGM', 'UNT'], self::outline($nodes));
        self::assertSame(0, $structure->groupCount());
        self::assertSame([], $structure->groups());
    }

    /**
     * @test
     */
    public function an_empty_group_is_skipped_rather_than_opened(): void
    {
        // A group with no parts has no trigger, so nothing can open it.
        $structure = new MessageStructure('ODD', [new SegmentGroup('SG1', 1, false, [])]);

        self::assertNull($structure->group('SG1')?->triggerTag());
        self::assertSame(
            ['UNH', 'BGM', 'UNT'],
            self::outline((new StructureGrouper())->group(self::parse("UNH+1+ODD'BGM+220'UNT+3+1'"), $structure)),
        );
    }

    /**
     * @test
     */
    public function a_group_whose_trigger_is_a_nested_group_resolves_through_it(): void
    {
        $inner = new SegmentGroup('SG2', 1, false, [new SegmentPosition('NAD', true, 1)]);
        $outer = new SegmentGroup('SG1', 1, false, [$inner]);

        self::assertSame('NAD', $outer->triggerTag());

        // UNH has to be in the structure for the matcher to reach the group: a position
        // only consumes the segment in front of it, so an unmodelled leading segment stops
        // the walk and lands in the ungrouped remainder.
        $structure = new MessageStructure('NESTED', [new SegmentPosition('UNH', true, 1), $outer]);
        $nodes = (new StructureGrouper())->group(self::parse("UNH+1+NESTED'NAD+BY'UNT+3+1'"), $structure);

        self::assertSame(['UNH', 'SG1#0 []', '  SG2#0 [NAD]', 'UNT'], self::outline($nodes));
    }

    /**
     * @test
     */
    public function the_real_orders_structure_groups_a_real_message(): void
    {
        $directory = XmlDirectory::locate('D96A');

        if ($directory === null) {
            self::markTestSkipped('php-edifact/edifact-mapping is not installed.');
        }

        $structure = $directory->messageStructure('ORDERS');
        self::assertNotNull($structure);
        // The heuristic in GroupingRules models one level; the directory defines dozens.
        self::assertGreaterThan(40, $structure->groupCount());

        $message = self::parse(
            "UNH+1+ORDERS:D:96A:UN'BGM+220'DTM+137:20240101:102'"
            . "RFF+ON:123'DTM+171:20231201:102'"
            . "NAD+BY+123::9'CTA+IC+:John'COM+j@e.com:EM'"
            . "NAD+SU+456::9'"
            . "LIN+1++ART1:BP'QTY+21:100'PRI+AAA:12.5'"
            . "LIN+2++ART2:BP'QTY+21:50'"
            . "UNS+S'UNT+16+1'",
        );

        self::assertSame([
            'UNH',
            'BGM',
            'DTM',
            'SG1#0 [RFF,DTM]',
            'SG2#0 [NAD]',
            '  SG5#0 [CTA,COM]',
            'SG2#1 [NAD]',
            'SG25#0 [LIN,QTY]',
            '  SG28#0 [PRI]',
            'SG25#1 [LIN,QTY]',
            'UNS',
            'UNT',
        ], self::outline((new StructureGrouper())->group($message, $structure)));
    }

    private static function structure(): MessageStructure
    {
        $structure = XmlDirectory::fromPath('TEST', self::FIXTURES . '/TEST')->messageStructure('TESTMSG');
        self::assertNotNull($structure);

        return $structure;
    }

    private static function parse(string $edi): TransactionMessage
    {
        return EdifactParser::createWithDefaultSegments()->parse($edi)->transactionMessages()[0];
    }

    /**
     * @param list<GroupInstance|SegmentInterface> $nodes
     *
     * @return list<string>
     */
    private static function outline(array $nodes): array
    {
        $lines = [];

        foreach ($nodes as $node) {
            if (!$node instanceof GroupInstance) {
                $lines[] = $node->tag();
                continue;
            }

            $tags = implode(',', array_map(static fn (SegmentInterface $s) => $s->tag(), $node->segments()));
            $lines[] = $node->id() . '#' . $node->occurrence() . ' [' . $tags . ']';

            foreach (self::outline($node->children()) as $child) {
                $lines[] = '  ' . $child;
            }
        }

        return $lines;
    }
}
