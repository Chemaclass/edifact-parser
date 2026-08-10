<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Diff;

use EdifactParser\Diff\Difference;
use EdifactParser\Diff\InterchangeDiff;
use EdifactParser\EdifactParser;
use EdifactParser\ParserResult;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\TransactionMessage;
use PHPUnit\Framework\TestCase;

final class InterchangeDiffTest extends TestCase
{
    /**
     * @test
     */
    public function identical_interchanges_have_no_differences(): void
    {
        $edi = "UNH+1+ORDERS:D:96A:UN'BGM+220'QTY+21:100'UNT+4+1'";

        $diff = new InterchangeDiff();

        self::assertSame([], $diff->diff(self::parse($edi), self::parse($edi)));
        self::assertTrue($diff->isIdentical(self::parse($edi), self::parse($edi)));
    }

    /**
     * @test
     */
    public function a_changed_value_is_reported_once(): void
    {
        $before = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:100'UNT+3+1'");
        $after = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:250'UNT+3+1'");

        $differences = (new InterchangeDiff())->diff($before, $after);

        self::assertCount(1, $differences);
        self::assertSame(Difference::CHANGED, $differences[0]->kind());
        self::assertSame('QTY', $differences[0]->tag());
        self::assertSame('21', $differences[0]->subId());
        self::assertSame(0, $differences[0]->messageIndex());
        self::assertSame(['QTY', ['21', '100']], $differences[0]->before()?->rawValues());
        self::assertSame(['QTY', ['21', '250']], $differences[0]->after()?->rawValues());
        self::assertFalse((new InterchangeDiff())->isIdentical($before, $after));
    }

    /**
     * @test
     */
    public function an_insertion_does_not_cascade_into_everything_after_it(): void
    {
        // This is the whole reason alignment uses an LCS rather than comparing by
        // position: with positional comparison every segment after the inserted DTM would
        // be reported as changed, burying the one real difference.
        $before = self::parse("UNH+1+ORDERS:D:96A:UN'BGM+220'NAD+BY+123'LIN+1'QTY+21:100'UNT+6+1'");
        $after = self::parse("UNH+1+ORDERS:D:96A:UN'BGM+220'DTM+137:20240101:102'NAD+BY+123'LIN+1'QTY+21:100'UNT+6+1'");

        self::assertSame(['+ message 0  DTM:137'], self::outline((new InterchangeDiff())->diff($before, $after)));
    }

    /**
     * @test
     */
    public function additions_removals_and_changes_are_reported_together(): void
    {
        $before = self::parse("UNH+1+ORDERS:D:96A:UN'BGM+220'NAD+BY+123'LIN+1'QTY+21:100'UNT+6+1'");
        $after = self::parse("UNH+1+ORDERS:D:96A:UN'BGM+220'DTM+137:20240101:102'LIN+1'QTY+21:250'UNT+6+1'");

        self::assertSame([
            '- message 0  NAD:BY',
            '+ message 0  DTM:137',
            '~ message 0  QTY:21',
        ], self::outline((new InterchangeDiff())->diff($before, $after)));
    }

    /**
     * @test
     */
    public function a_message_added_to_the_interchange_is_reported_wholesale(): void
    {
        $before = self::parse("UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'BGM+220'UNT+3+1'UNZ+1+1'");
        $after = self::parse(
            "UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'BGM+220'UNT+3+1'"
            . "UNH+2+ORDERS:D:96A:UN'BGM+221'UNT+3+2'UNZ+2+1'",
        );

        $differences = (new InterchangeDiff())->diff($before, $after);

        self::assertSame([Difference::ADDED, Difference::ADDED, Difference::ADDED], array_map(
            static fn (Difference $d) => $d->kind(),
            $differences,
        ));
        self::assertSame([1, 1, 1], array_map(static fn (Difference $d) => $d->messageIndex(), $differences));
    }

    /**
     * @test
     */
    public function a_message_removed_from_the_interchange_is_reported_wholesale(): void
    {
        $before = self::parse(
            "UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'BGM+220'UNT+3+1'"
            . "UNH+2+ORDERS:D:96A:UN'BGM+221'UNT+3+2'UNZ+2+1'",
        );
        $after = self::parse("UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'BGM+220'UNT+3+1'UNZ+1+1'");

        $differences = (new InterchangeDiff())->diff($before, $after);

        self::assertNotEmpty($differences);
        foreach ($differences as $difference) {
            self::assertSame(Difference::REMOVED, $difference->kind());
            self::assertSame(1, $difference->messageIndex());
            self::assertNull($difference->after());
            self::assertNotNull($difference->before());
        }
    }

    /**
     * @test
     */
    public function two_messages_can_be_compared_directly(): void
    {
        $before = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:100'UNT+3+1'")->transactionMessages()[0];
        $after = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:250'UNT+3+1'")->transactionMessages()[0];

        $differences = (new InterchangeDiff())->diffMessages($before, $after, 7);

        self::assertCount(1, $differences);
        self::assertSame(7, $differences[0]->messageIndex());
    }

    /**
     * @test
     */
    public function differences_serialise(): void
    {
        $before = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:100'UNT+3+1'");
        $after = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:250'UNT+3+1'");

        $data = (new InterchangeDiff())->diff($before, $after)[0]->toArray();

        self::assertSame([
            'kind' => 'changed',
            'message' => 0,
            'tag' => 'QTY',
            'subId' => '21',
            'before' => ['QTY', ['21', '100']],
            'after' => ['QTY', ['21', '250']],
        ], $data);
    }

    /**
     * @test
     */
    public function an_added_difference_has_no_before_and_a_removed_one_no_after(): void
    {
        // Note the UNT counts differ too, and legitimately so: adding a segment changes
        // the trailer's segment count, hence its subId. Pick out the QTY difference
        // rather than assuming it is first.
        $empty = self::parse("UNH+1+ORDERS:D:96A:UN'UNT+2+1'");
        $withQty = self::parse("UNH+1+ORDERS:D:96A:UN'QTY+21:100'UNT+3+1'");

        $added = self::forTag((new InterchangeDiff())->diff($empty, $withQty), 'QTY');
        self::assertNotNull($added);
        self::assertSame(Difference::ADDED, $added->kind());
        self::assertNull($added->before());
        self::assertNotNull($added->after());
        self::assertNull($added->toArray()['before']);

        $removed = self::forTag((new InterchangeDiff())->diff($withQty, $empty), 'QTY');
        self::assertNotNull($removed);
        self::assertSame(Difference::REMOVED, $removed->kind());
        self::assertNull($removed->after());
        self::assertNull($removed->toArray()['after']);
    }

    /**
     * @test
     */
    public function trailing_segments_with_no_counterpart_are_reported_as_removed(): void
    {
        // Real interchanges always end with UNT, so the right-hand list is never a strict
        // prefix of the left in practice. Constructing the messages reaches that branch of
        // the alignment directly.
        $factory = SegmentFactory::withDefaultSegments();
        $unh = $factory->createSegmentFromArray(['UNH', '1', ['ORDERS', 'D', '96A', 'UN']]);
        $bgm = $factory->createSegmentFromArray(['BGM', '220']);
        $qty = $factory->createSegmentFromArray(['QTY', ['21', '100']]);

        $before = new TransactionMessage([], [], [], [$unh, $bgm, $qty]);
        $after = new TransactionMessage([], [], [], [$unh]);

        self::assertSame(
            ['- message 0  BGM:220', '- message 0  QTY:21'],
            self::outline((new InterchangeDiff())->diffMessages($before, $after)),
        );

        // And the mirror image, so both leftover branches are covered.
        self::assertSame(
            ['+ message 0  BGM:220', '+ message 0  QTY:21'],
            self::outline((new InterchangeDiff())->diffMessages($after, $before)),
        );
    }

    /**
     * @test
     */
    public function comparing_an_empty_interchange_against_itself_is_identical(): void
    {
        $empty = self::parse("UNB+UNOC:3+S+R+240101:1200+1'UNZ+0+1'");

        self::assertTrue((new InterchangeDiff())->isIdentical($empty, $empty));
    }
    private static function parse(string $edi): ParserResult
    {
        return EdifactParser::createWithDefaultSegments()->parse($edi);
    }

    /**
     * @param list<Difference> $differences
     *
     * @return list<string>
     */
    private static function outline(array $differences): array
    {
        return array_map(static fn (Difference $d): string => (string) $d, $differences);
    }

    /**
     * @param list<Difference> $differences
     */
    private static function forTag(array $differences, string $tag): ?Difference
    {
        foreach ($differences as $difference) {
            if ($difference->tag() === $tag) {
                return $difference;
            }
        }

        return null;
    }
}
