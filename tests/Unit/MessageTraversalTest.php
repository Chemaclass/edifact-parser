<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\ContextSegment;
use EdifactParser\EdifactParser;
use EdifactParser\GroupingRules;
use EdifactParser\ParserResult;
use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Serializer\EdifactSerializer;
use EdifactParser\TransactionMessage;
use PHPUnit\Framework\TestCase;

/**
 * Countable/IteratorAggregate behaviour and the plain-array views on the parse result.
 */
final class MessageTraversalTest extends TestCase
{
    private const ORDER = <<<'EDI'
        UNB+UNOC:3+SENDER+RECIPIENT+20240101:1200+REF01'
        UNH+1+ORDERS:D:96A:UN'
        BGM+220+ORD1+9'
        NAD+BY+BUYER::9'
        CTA+IC+:John Doe'
        LIN+1++ART1:BP'
        QTY+21:100'
        UNS+S'
        CNT+2:1'
        UNT+9+1'
        UNH+2+INVOIC:D:96A:UN'
        BGM+380+INV1+9'
        UNT+3+2'
        UNZ+2+REF01'
        EDI;

    /**
     * @test
     */
    public function a_result_is_countable_and_iterable_over_its_messages(): void
    {
        $result = $this->parse();

        self::assertInstanceOf(ParserResult::class, $result);
        self::assertCount(2, $result);

        $types = [];
        foreach ($result as $message) {
            $types[] = $message->messageType();
        }

        self::assertSame(['ORDERS', 'INVOIC'], $types);
    }

    /**
     * @test
     */
    public function messages_can_be_picked_by_position_and_by_type(): void
    {
        $result = $this->parse();

        self::assertSame('ORDERS', $result->firstMessage()?->messageType());
        self::assertCount(1, $result->messagesOfType('INVOIC'));
        self::assertSame([], $result->messagesOfType('DESADV'));
    }

    /**
     * @test
     */
    public function an_empty_interchange_has_no_first_message(): void
    {
        $result = EdifactParser::createWithDefaultSegments()->parse("UNB+UNOC:3+S+R+20240101:1200+1'UNZ+0+1'");

        self::assertNull($result->firstMessage());
        self::assertCount(0, $result);
    }

    /**
     * @test
     */
    public function a_message_is_iterable_in_document_order(): void
    {
        $message = $this->parse()->transactionMessages()[0];

        $tags = [];
        foreach ($message as $segment) {
            $tags[] = $segment->tag();
        }

        self::assertSame(['UNH', 'BGM', 'NAD', 'CTA', 'LIN', 'QTY', 'UNS', 'CNT', 'UNT'], $tags);
        self::assertCount(9, $message);
    }

    /**
     * @test
     */
    public function an_iterable_message_can_be_serialized_back_directly(): void
    {
        $message = $this->parse()->transactionMessages()[1];

        self::assertSame(
            "UNH+2+INVOIC:D:96A:UN'\nBGM+380+INV1+9'\nUNT+3+2'",
            (new EdifactSerializer())->serialize($message),
        );
    }

    /**
     * @test
     */
    public function tags_are_counted_once_and_answered_from_the_index(): void
    {
        $message = $this->parse()->transactionMessages()[0];

        self::assertSame(
            ['UNH' => 1, 'BGM' => 1, 'NAD' => 1, 'CTA' => 1, 'LIN' => 1, 'QTY' => 1, 'UNS' => 1, 'CNT' => 1, 'UNT' => 1],
            $message->countByTag(),
        );
        // Second call comes from the memoized map.
        self::assertSame($message->countByTag(), $message->countByTag());

        self::assertTrue($message->has('QTY'));
        self::assertFalse($message->has('PRI'));
    }

    /**
     * @test
     */
    public function a_message_describes_itself_as_array_and_json(): void
    {
        $message = $this->parse()->transactionMessages()[1];

        self::assertSame([
            'type' => 'INVOIC',
            'segments' => [
                ['tag' => 'UNH', 'subId' => '2', 'rawValues' => ['UNH', '2', ['INVOIC', 'D', '96A', 'UN']]],
                ['tag' => 'BGM', 'subId' => '380', 'rawValues' => ['BGM', '380', 'INV1', '9']],
                ['tag' => 'UNT', 'subId' => '3', 'rawValues' => ['UNT', '3', '2']],
            ],
        ], $message->toArray());

        self::assertSame($message->toArray(), json_decode($message->toJson(), true));
    }

    /**
     * @test
     */
    public function a_message_built_from_the_keyed_map_falls_back_to_flattening_it(): void
    {
        $parsed = $this->parse()->transactionMessages()[0];
        // The keyed map holds the header/summary segments; LIN and QTY live in the line items.
        $fromMap = new TransactionMessage($parsed->allSegments());

        self::assertSame(['UNH', 'BGM', 'NAD', 'CTA', 'UNS', 'CNT', 'UNT'], array_keys($fromMap->countByTag()));
        self::assertSame(7, $fromMap->count());
        self::assertCount(7, iterator_to_array($fromMap));
        self::assertSame([], $fromMap->segments());
    }

    /**
     * @test
     */
    public function a_line_item_is_countable_and_iterable(): void
    {
        $lineItem = $this->parse()->transactionMessages()[0]->lineItems()[1];

        self::assertCount(2, $lineItem);
        self::assertSame(['LIN', 'QTY'], array_map(
            static fn (SegmentInterface $segment) => $segment->tag(),
            iterator_to_array($lineItem),
        ));
    }

    /**
     * @test
     */
    public function a_functional_group_is_countable_and_iterable(): void
    {
        $edi = <<<'EDI'
            UNB+UNOC:3+SENDER+RECIPIENT+20240101:1200+REF01'
            UNG+ORDERS+S1+R1+20240101:1200+1+UN+D:96A'
            UNH+1+ORDERS:D:96A:UN'
            BGM+220'
            UNT+3+1'
            UNE+1+1'
            UNZ+1+REF01'
            EDI;

        $group = EdifactParser::createWithDefaultSegments()->parse($edi)->functionalGroups()[0];

        self::assertCount(1, $group);
        self::assertSame(['ORDERS'], array_map(
            static fn (TransactionMessage $message) => $message->messageType(),
            iterator_to_array($group),
        ));
    }

    /**
     * @test
     */
    public function keyed_views_return_the_typed_segment_and_contexts_are_looked_up(): void
    {
        $message = $this->parse()->transactionMessages()[0];

        $buyer = $message->segmentByTagAndSubId('NAD', 'BY');
        self::assertInstanceOf(NADNameAddress::class, $buyer);
        // The typed accessors are the point: this used to be a ContextSegment and fatal.
        self::assertSame('BY', $buyer->partyQualifier());
        self::assertSame('BUYER', $buyer->partyId());

        $lineItem = $message->lineItemById(1)?->segmentByTagAndSubId('LIN', '1');
        self::assertInstanceOf(LINLineItem::class, $lineItem);

        self::assertInstanceOf(ContextSegment::class, $message->contextFor($buyer));
        self::assertSame(['CTA'], array_map(
            static fn (SegmentInterface $child) => $child->tag(),
            $message->childrenOf($buyer),
        ));
        self::assertSame(['QTY'], array_map(
            static fn (SegmentInterface $child) => $child->tag(),
            $message->childrenOf($lineItem),
        ));
    }

    /**
     * @test
     */
    public function contexts_resolve_from_either_the_segment_or_the_context_itself(): void
    {
        $message = $this->parse()->transactionMessages()[0];
        $context = $message->contextSegments()[0];

        self::assertSame($context, $message->contextFor($context));
        self::assertSame($context->children(), $message->childrenOf($context));
    }

    /**
     * @test
     */
    public function grouping_rules_expose_their_configured_tags(): void
    {
        $rules = GroupingRules::default();

        self::assertSame(GroupingRules::DEFAULT_CONTEXT_TAGS, $rules->contextTags());
        self::assertSame(GroupingRules::DEFAULT_CHILD_TAGS, $rules->childTags());
        self::assertSame(GroupingRules::DEFAULT_BREAK_LINE_ITEM_TAGS, $rules->breakLineItemTags());

        $custom = $rules->withContextTags(['NAD'])->withChildTags(['CTA'])->withBreakLineItemTags(['UNT']);

        self::assertSame(['NAD'], $custom->contextTags());
        self::assertSame(['CTA'], $custom->childTags());
        self::assertSame(['UNT'], $custom->breakLineItemTags());
        // The original is untouched.
        self::assertSame(GroupingRules::DEFAULT_CONTEXT_TAGS, $rules->contextTags());
    }

    private function parse(): ParserResult
    {
        return EdifactParser::createWithDefaultSegments()->parse(self::ORDER);
    }
}
