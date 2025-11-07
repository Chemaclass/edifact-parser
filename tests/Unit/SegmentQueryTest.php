<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\SegmentQuery;
use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;
use PHPUnit\Framework\TestCase;

final class SegmentQueryTest extends TestCase
{
    /**
     * @test
     */
    public function filter_by_tag(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new NADNameAddress(['NAD', 'SU']),
            new LINLineItem(['LIN', '1']),
        ];

        $query = new SegmentQuery($segments);
        $result = $query->withTag('NAD')->get();

        self::assertCount(2, $result);
        self::assertEquals('NAD', $result[0]->tag());
        self::assertEquals('NAD', $result[1]->tag());
    }

    /**
     * @test
     */
    public function filter_by_multiple_tags(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new LINLineItem(['LIN', '1']),
            new QTYQuantity(['QTY', ['21', '100']]),
        ];

        $query = new SegmentQuery($segments);
        $result = $query->withTags(['NAD', 'QTY'])->get();

        self::assertCount(2, $result);
        self::assertEquals('NAD', $result[0]->tag());
        self::assertEquals('QTY', $result[1]->tag());
    }

    /**
     * @test
     */
    public function filter_by_subid(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new NADNameAddress(['NAD', 'SU']),
            new NADNameAddress(['NAD', 'BY']),
        ];

        $query = new SegmentQuery($segments);
        $result = $query->withSubId('CN')->get();

        self::assertCount(1, $result);
        self::assertEquals('CN', $result[0]->subId());
    }

    /**
     * @test
     */
    public function filter_by_type(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new LINLineItem(['LIN', '1']),
            new NADNameAddress(['NAD', 'SU']),
        ];

        $query = new SegmentQuery($segments);
        $result = $query->ofType(NADNameAddress::class)->get();

        self::assertCount(2, $result);
        self::assertInstanceOf(NADNameAddress::class, $result[0]);
        self::assertInstanceOf(NADNameAddress::class, $result[1]);
    }

    /**
     * @test
     */
    public function chain_multiple_filters(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new NADNameAddress(['NAD', 'SU']),
            new LINLineItem(['LIN', '1']),
            new NADNameAddress(['NAD', 'BY']),
        ];

        $query = new SegmentQuery($segments);
        $result = $query
            ->withTag('NAD')
            ->withSubId('CN')
            ->get();

        self::assertCount(1, $result);
        self::assertEquals('NAD', $result[0]->tag());
        self::assertEquals('CN', $result[0]->subId());
    }

    /**
     * @test
     */
    public function custom_where_predicate(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN', [], '', 'Company A']),
            new NADNameAddress(['NAD', 'SU', [], '', 'Company B']),
            new NADNameAddress(['NAD', 'BY', [], '', 'Company C']),
        ];

        $query = new SegmentQuery($segments);
        $result = $query
            ->where(static fn ($s) => $s->rawValues()[4] === 'Company B')
            ->get();

        self::assertCount(1, $result);
        self::assertEquals('Company B', $result[0]->rawValues()[4]);
    }

    /**
     * @test
     */
    public function limit_results(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new NADNameAddress(['NAD', 'SU']),
            new NADNameAddress(['NAD', 'BY']),
        ];

        $query = new SegmentQuery($segments);
        $result = $query->limit(2)->get();

        self::assertCount(2, $result);
    }

    /**
     * @test
     */
    public function skip_results(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new NADNameAddress(['NAD', 'SU']),
            new NADNameAddress(['NAD', 'BY']),
        ];

        $query = new SegmentQuery($segments);
        $result = $query->skip(1)->get();

        self::assertCount(2, $result);
        self::assertEquals('SU', $result[0]->subId());
    }

    /**
     * @test
     */
    public function first_segment(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new NADNameAddress(['NAD', 'SU']),
        ];

        $query = new SegmentQuery($segments);
        $result = $query->first();

        self::assertNotNull($result);
        self::assertEquals('CN', $result->subId());
    }

    /**
     * @test
     */
    public function first_returns_null_when_empty(): void
    {
        $query = new SegmentQuery([]);
        $result = $query->first();

        self::assertNull($result);
    }

    /**
     * @test
     */
    public function last_segment(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new NADNameAddress(['NAD', 'SU']),
        ];

        $query = new SegmentQuery($segments);
        $result = $query->last();

        self::assertNotNull($result);
        self::assertEquals('SU', $result->subId());
    }

    /**
     * @test
     */
    public function count_segments(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new NADNameAddress(['NAD', 'SU']),
            new NADNameAddress(['NAD', 'BY']),
        ];

        $query = new SegmentQuery($segments);

        self::assertEquals(3, $query->count());
        self::assertEquals(2, $query->withTag('NAD')->limit(2)->count());
    }

    /**
     * @test
     */
    public function exists_check(): void
    {
        $segments = [new NADNameAddress(['NAD', 'CN'])];

        $query = new SegmentQuery($segments);

        self::assertTrue($query->exists());
        self::assertFalse($query->withTag('LIN')->exists());
    }

    /**
     * @test
     */
    public function is_empty_check(): void
    {
        $query = new SegmentQuery([]);

        self::assertTrue($query->isEmpty());

        $query2 = new SegmentQuery([new NADNameAddress(['NAD', 'CN'])]);
        self::assertFalse($query2->isEmpty());
    }

    /**
     * @test
     */
    public function map_segments(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new NADNameAddress(['NAD', 'SU']),
        ];

        $query = new SegmentQuery($segments);
        $tags = $query->map(static fn ($s) => $s->tag());

        self::assertEquals(['NAD', 'NAD'], $tags);
    }

    /**
     * @test
     */
    public function each_segment(): void
    {
        $segments = [
            new NADNameAddress(['NAD', 'CN']),
            new NADNameAddress(['NAD', 'SU']),
        ];

        $query = new SegmentQuery($segments);
        $collected = [];

        $query->each(static function ($s) use (&$collected): void {
            $collected[] = $s->subId();
        });

        self::assertEquals(['CN', 'SU'], $collected);
    }
}
