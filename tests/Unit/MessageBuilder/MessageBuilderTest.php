<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\MessageBuilder;

use EdifactParser\LineItem;
use EdifactParser\MessageDataBuilder\Builder as MessageDataBuilder;
use EdifactParser\Segments\DTMDateTimePeriod;
use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Segments\RFFReference;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNSSectionControl;
use PHPUnit\Framework\TestCase;

class MessageBuilderTest extends TestCase
{
    private SegmentInterface $referenceSegment;
    private SegmentInterface $dateTimeSegment;
    private SegmentInterface $otherDateTimeSegment;
    private SegmentInterface $lineSegment;
    private SegmentInterface $quantitySegment;
    private SegmentInterface $otherLineSegment;
    private SegmentInterface $otherQuantitySegment;
    private SegmentInterface $separatorBetweenDetailsAndSummarySegment;

    protected function setUp(): void
    {
        $this->referenceSegment = new RFFReference(['RFF', ['ADE', '123433']]);
        $this->dateTimeSegment = new DTMDateTimePeriod(['DTM', ['10', '20191002', '102']]);
        $this->otherDateTimeSegment = new DTMDateTimePeriod(['DTM', ['15', '2022310', '102']]);
        $this->lineSegment = new LINLineItem(['LIN', '1']);
        $this->quantitySegment = new QTYQuantity(['QTY', ['21', '5']]);
        $this->otherLineSegment = new LINLineItem(['LIN', '2']);
        $this->otherQuantitySegment = new QTYQuantity(['QTY', ['23', '8']]);
        $this->separatorBetweenDetailsAndSummarySegment = new UNSSectionControl(['UNS', 'S']);
    }

    /**
     * @test
     */
    public function adds_segments(): void
    {
        $builder = new MessageDataBuilder();

        $builder->addSegment($this->referenceSegment);
        $builder->addSegment($this->dateTimeSegment);

        self::assertEquals([
            'RFF' => [
                'ADE' => $this->referenceSegment,
            ],
            'DTM' => [
                '10' => $this->dateTimeSegment,
            ],
        ], $builder->buildSegments());
    }

    /**
     * @test
     */
    public function groups_segments(): void
    {
        $builder = new MessageDataBuilder();

        $builder->addSegment($this->dateTimeSegment);
        $builder->addSegment($this->referenceSegment);
        $builder->addSegment($this->otherDateTimeSegment);

        self::assertEquals([
            'RFF' => [
                'ADE' => $this->referenceSegment,
            ],
            'DTM' => [
                '10' => $this->dateTimeSegment,
                '15' => $this->otherDateTimeSegment,
            ],
        ], $builder->buildSegments());
    }

    /**
     * @test
     */
    public function build_line_items(): void
    {
        $builder = new MessageDataBuilder();

        $builder->addSegment($this->dateTimeSegment);
        $builder->addSegment($this->lineSegment);
        $builder->addSegment($this->quantitySegment);
        $builder->addSegment($this->otherLineSegment);
        $builder->addSegment($this->otherQuantitySegment);
        $builder->addSegment($this->separatorBetweenDetailsAndSummarySegment);

        self::assertEquals([
            'DTM' => [
                '10' => $this->dateTimeSegment,
            ],

            'UNS' => [
                'S' => $this->separatorBetweenDetailsAndSummarySegment,
            ],
        ], $builder->buildSegments());

        self::assertEquals([
            '1' => new LineItem([
                'LIN' => ['1' => $this->lineSegment],
                'QTY' => ['21' => $this->quantitySegment],
            ]),
            '2' => new LineItem([
                'LIN' => ['2' => $this->otherLineSegment],
                'QTY' => ['23' => $this->otherQuantitySegment],
            ]),
        ], $builder->buildLineItems());
    }

    /**
     * @test
     */
    public function groups_segments_within_line_items(): void
    {
        $builder = new MessageDataBuilder();

        $builder->addSegment($this->lineSegment);
        $builder->addSegment($this->quantitySegment);
        $builder->addSegment($this->otherQuantitySegment);
        $builder->addSegment($this->separatorBetweenDetailsAndSummarySegment);

        self::assertEquals([
            'UNS' => [
                'S' => $this->separatorBetweenDetailsAndSummarySegment,
            ],
        ], $builder->buildSegments());

        self::assertEquals([
            '1' => new LineItem([
                'LIN' => ['1' => $this->lineSegment],
                'QTY' => [
                    '21' => $this->quantitySegment,
                    '23' => $this->otherQuantitySegment,
                ],
            ]),
        ], $builder->buildLineItems());
    }
}
