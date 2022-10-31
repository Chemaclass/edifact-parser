<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNSSectionControl;

class MessageBuilder extends SimpleMessageBuilder
{
    private array $builders = [];
    private SectionalMessageBuilder $currentBuilder;

    public function __construct()
    {
        $this->setCurrentBuilder(new Normal());
    }

    public function addSegment(SegmentInterface $segment): self
    {
        $this->updateState($segment);
        $this->currentBuilder->addSegment($segment);
        return $this;
    }

    public function build(): array
    {
        $data = [];

        foreach ($this->builders as $builder) {
            $data = array_merge($data, $builder->build());
        }

        return $data;
    }

    public function updateState(SegmentInterface $segment): void
    {
        if ($this->isAtStartOfDetailsSection($segment)) {
            $this->setCurrentBuilder(new LineItems());
        } elseif ($this->atEndOfDetailsSection($segment)) {
            $this->setCurrentBuilder(new Normal());
        }
    }

    public function isAtStartOfDetailsSection(SegmentInterface $segment): bool
    {
        return $segment instanceof LINLineItem && $this->currentBuilder instanceof Normal;
    }

    private function setCurrentBuilder(SectionalMessageBuilder $builder): void
    {
        $this->currentBuilder = $builder;
        $this->builders[] = $builder;
    }

    private function atEndOfDetailsSection(SegmentInterface $segment): bool
    {
        return $segment instanceof UNSSectionControl &&
            $segment->getIdentifier()->indicatesEndOfDetailsSection();
    }
}
