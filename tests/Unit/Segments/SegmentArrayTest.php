<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Segments;

use ArrayIterator;
use EdifactParser\ContextSegment;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Segments\SegmentArray;
use PHPUnit\Framework\TestCase;

final class SegmentArrayTest extends TestCase
{
    /**
     * @test
     */
    public function plain_segment_has_no_children_key(): void
    {
        $segment = new NADNameAddress(['NAD', 'BY']);

        self::assertSame(
            ['tag' => 'NAD', 'subId' => 'BY', 'rawValues' => ['NAD', 'BY']],
            SegmentArray::fromSegment($segment),
        );
    }

    /**
     * @test
     */
    public function context_segment_nests_its_children(): void
    {
        $context = new ContextSegment(
            new NADNameAddress(['NAD', 'BY']),
            [new QTYQuantity(['QTY', ['21', '5']])],
        );

        self::assertSame([
            'tag' => 'NAD',
            'subId' => 'BY',
            'rawValues' => ['NAD', 'BY'],
            'children' => [
                ['tag' => 'QTY', 'subId' => '21', 'rawValues' => ['QTY', ['21', '5']]],
            ],
        ], SegmentArray::fromSegment($context));
    }

    /**
     * @test
     */
    public function nested_contexts_recurse(): void
    {
        $inner = new ContextSegment(new QTYQuantity(['QTY', ['21', '5']]));
        $outer = new ContextSegment(new NADNameAddress(['NAD', 'BY']), [$inner]);

        self::assertSame([
            'tag' => 'NAD',
            'subId' => 'BY',
            'rawValues' => ['NAD', 'BY'],
            'children' => [
                [
                    'tag' => 'QTY',
                    'subId' => '21',
                    'rawValues' => ['QTY', ['21', '5']],
                    'children' => [],
                ],
            ],
        ], SegmentArray::fromSegment($outer));
    }

    /**
     * @test
     */
    public function converts_any_iterable_of_segments(): void
    {
        $segments = new ArrayIterator([
            new NADNameAddress(['NAD', 'BY']),
            new NADNameAddress(['NAD', 'SU']),
        ]);

        self::assertSame(['BY', 'SU'], array_column(SegmentArray::fromSegments($segments), 'subId'));
    }
}
