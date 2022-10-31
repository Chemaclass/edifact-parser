<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNSSectionControl;

class MessageBuilder extends SimpleMessageBuilder
{
    private ?SimpleMessageBuilder $lineItemsBuilder = null;
    private string|null $lineItemId = null;

    public function addSegment(SegmentInterface $segment): self
    {
        $this->updateStateOfLineProcessing($segment);
        $this->_addSegment($segment);
        return $this;
    }

    public function processLineItem(SegmentInterface $segment): void
    {
        $this->saveLineItemsIfPresent();
        $this->lineItemsBuilder = new SimpleMessageBuilder();
        $this->lineItemId = $segment->subId();
    }

    public function endProcessingOfLineItems(): void
    {
        $this->saveLineItemsIfPresent();
        $this->lineItemsBuilder = null;
    }

    public function saveLineItemsIfPresent(): void
    {
        if ($this->lineItemsBuilder) {
            $segments = $this->lineItemsBuilder->build();
            $this->data['LIN'][$this->lineItemId] = $segments;
        }
    }

    public function updateStateOfLineProcessing(SegmentInterface $segment): void
    {
        if ($this->indicatesEndOfDetailsSection($segment)) {
            $this->endProcessingOfLineItems();
        } elseif ($segment instanceof LINLineItem) {
            $this->processLineItem($segment);
        }
    }

    public function _addSegment(SegmentInterface $segment): void
    {
        if ($this->lineItemsBuilder) {
            $this->saveLineItemData($segment);
        } else {
            parent::addSegment($segment);
        }
    }

    private function saveLineItemData(SegmentInterface $segment): void
    {
        $this->lineItemsBuilder?->addSegment($segment);
    }

    private function indicatesEndOfDetailsSection(SegmentInterface $segment): bool
    {
        return $segment instanceof UNSSectionControl &&
            $segment->getIdentifier()->indicatesEndOfDetailsSection();
    }
}
