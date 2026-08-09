<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\MessageDataBuilder;

use EdifactParser\EdifactParser;
use EdifactParser\LineItem;
use EdifactParser\MessageDataBuilder\Builder;
use EdifactParser\Segments\DTMDateTimePeriod;
use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Segments\UNSSectionControl;
use PHPUnit\Framework\TestCase;

final class BuilderTest extends TestCase
{
    /**
     * @test
     */
    public function segments_of_the_same_tag_survive_on_both_sides_of_the_details_section(): void
    {
        // A DTM in the header and another one in the summary: the summary one used to be
        // dropped, because the whole 'DTM' entry was already taken by the header section.
        $edi = <<<'EDI'
            UNH+1+ORDERS:D:96A:UN'
            DTM+137:20240101:102'
            LIN+1++ART1:BP'
            QTY+21:100'
            UNS+S'
            DTM+203:20240202:102'
            UNT+7+1'
            EDI;

        $message = EdifactParser::createWithDefaultSegments()->parse($edi)->transactionMessages()[0];

        $dtmSegments = $message->segmentsByTag('DTM');

        self::assertCount(2, $dtmSegments);
        self::assertNotNull($message->segmentByTagAndSubId('DTM', '137'));
        self::assertNotNull($message->segmentByTagAndSubId('DTM', '203'));
    }

    /**
     * @test
     */
    public function line_item_data_is_the_unwrapped_form_of_the_line_items(): void
    {
        $builder = new Builder();
        $builder
            ->addSegment(new DTMDateTimePeriod(['DTM', ['137', '20240101', '102']]))
            ->addSegment(new LINLineItem(['LIN', '1']))
            ->addSegment(new QTYQuantity(['QTY', ['21', '100']]))
            ->addSegment(new UNSSectionControl(['UNS', 'S']));

        $data = $builder->buildLineItemData();

        self::assertSame(['1'], array_map('strval', array_keys($data)));
        self::assertSame(['LIN', 'QTY'], array_keys($data['1']));
        self::assertEquals(
            array_map(static fn (LineItem $lineItem) => $lineItem->allSegments(), $builder->buildLineItems()),
            $data,
        );
    }
}
