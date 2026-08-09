<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\ContextSegment;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Segments\UnknownSegment;
use PHPUnit\Framework\TestCase;

final class ContextSegmentTest extends TestCase
{
    public function test_tag_and_sub_id_proxy_parent_segment(): void
    {
        $segment = new UnknownSegment(['NAD', 'CN']);
        $context = new ContextSegment($segment);

        self::assertSame('NAD', $context->tag());
        self::assertSame('CN', $context->subId());
    }

    public function test_parsed_sub_id_and_raw_values_proxy_parent_segment(): void
    {
        $rawValues = ['NAD', ['CN', 'sub']];
        $context = new ContextSegment(new UnknownSegment($rawValues));

        self::assertSame(['CN', 'sub'], $context->parsedSubId());
        self::assertSame($rawValues, $context->rawValues());
    }

    public function test_children_can_be_looked_up_by_tag(): void
    {
        $firstQty = new QTYQuantity(['QTY', ['21', '5']]);
        $secondQty = new QTYQuantity(['QTY', ['12', '3']]);
        $context = new ContextSegment(new NADNameAddress(['NAD', 'BY']), [$firstQty, $secondQty]);

        self::assertSame([$firstQty, $secondQty], $context->childrenByTag('QTY'));
        self::assertSame($firstQty, $context->childByTag('QTY'));
        self::assertSame([], $context->childrenByTag('PRI'));
        self::assertNull($context->childByTag('PRI'));
    }

    public function test_is_countable_and_iterable_over_its_children(): void
    {
        $child = new QTYQuantity(['QTY', ['21', '5']]);
        $context = new ContextSegment(new NADNameAddress(['NAD', 'BY']), [$child]);

        self::assertCount(1, $context);
        self::assertTrue($context->hasChildren());
        self::assertSame([$child], iterator_to_array($context));
    }

    public function test_a_context_without_children_is_empty(): void
    {
        $context = new ContextSegment(new NADNameAddress(['NAD', 'BY']));

        self::assertCount(0, $context);
        self::assertFalse($context->hasChildren());
    }

    public function test_to_array_and_to_json_include_the_children(): void
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
        ], $context->toArray());

        self::assertSame($context->toArray(), json_decode($context->toJson(), true));
    }
}
