<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNSSectionControl;

class MessageBuilder
{
    private array $data;
    private ?SimpleMessageBuilder $lineItemsBuilder = null;
    private string|null $lineItemId = null;

    public function addSegment(SegmentInterface $segment): self
    {
        if ($this->indicatesEndOfDetailsSection($segment)) {
            $this->endProcessingOfLineItems();
        } elseif ($segment instanceof LINLineItem) {
            $this->processLineItem($segment);
        }

        if ($this->lineItemsBuilder) {
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

    private function saveSegment(SegmentInterface $segment): void
    {
        $this->data[$segment->tag()] ??= [];
        $this->data[$segment->tag()][$segment->subId()] = $segment;
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
