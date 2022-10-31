<?php

declare(strict_types=1);

namespace EdifactParser\MessageDataBuilder;

use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNSSectionControl;

class Builder extends SimpleBuilder
{
    use MultipleBuilderWrapper;

    public function __construct()
    {
        $this->setCurrentBuilder(new SimpleBuilder());
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
        if ($this->atStartOfDetailsSection($segment)) {
            $this->setCurrentBuilder(new DetailsSectionBuilder());
        }

        if ($this->atEndOfDetailsSection($segment)) {
            $this->setCurrentBuilder(new SimpleBuilder());
        }
    }

    private function atStartOfDetailsSection(SegmentInterface $segment): bool
    {
        return $segment instanceof LINLineItem
            && !($this->currentBuilder instanceof DetailsSectionBuilder);
    }

    private function atEndOfDetailsSection(SegmentInterface $segment): bool
    {
        return $segment instanceof UNSSectionControl
            && $segment->indicatesEndOfDetailsSection();
    }
}
