<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNSSectionControl;

class MessageBuilder
{
    private array $data;
    private array $lineItemData;
    private bool $isProcessingLineItems = false;

    public function addSegment(SegmentInterface $segment): self
    {
        if ($this->indicatesEndOfDetailsSection($segment)) {
            $this->endProcessingOfLineItems();
        } elseif ($segment instanceof LINLineItem) {
            $this->processLineItem($segment);
        }

        if ($this->isProcessingLineItems) {
            $this->saveLineItemData($segment);
        } else {
            $this->saveSegment($segment);
        }

        return $this;
    }

    public function build(): array
    {
        return $this->data;
    }

    public function processLineItem(SegmentInterface $segment): void
    {
        $this->isProcessingLineItems = true;
        $this->lineItemData[$segment->subId()] = [];
    }

    public function endProcessingOfLineItems(): void
    {
        $this->isProcessingLineItems = false;
        $this->saveLineItemDataIfPresent();
    }

    private function saveSegment(SegmentInterface $segment): void
    {
        $this->data[$segment->tag()] ??= [];
        $this->data[$segment->tag()][$segment->subId()] = $segment;
    }

    private function saveLineItemData(SegmentInterface $segment): void
    {
        if (!empty($this->lineItemData)) {
            $lastKey = array_key_last($this->lineItemData);
            $this->lineItemData[$lastKey][] = $segment;
        }
    }

    private function indicatesEndOfDetailsSection(SegmentInterface $segment): bool
    {
        return $segment instanceof UNSSectionControl &&
            $segment->getIdentifier()->indicatesEndOfDetailsSection();
    }

    private function saveLineItemDataIfPresent(): void
    {
        if (!empty($this->lineItemData)) {
            $this->data['LIN'] = $this->lineItemData;
        }
    }
}
