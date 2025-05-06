<?php

declare(strict_types=1);

namespace EdifactParser\MessageDataBuilder;

use EdifactParser\Segments\SegmentInterface;

class DetailsSectionBuilder implements BuilderInterface
{
    use MultipleBuilderWrapper;

    private const BEGINNING_TAGS = ['LIN'];
    private const ENDING_TAGS = ['UNS', 'UNT'];

    public function addSegment(SegmentInterface $segment): self
    {
        if (in_array($segment->tag(), self::BEGINNING_TAGS)) {
            $this->setCurrentBuilder(new SimpleBuilder(), $segment->subId());
        } elseif (in_array($segment->tag(), self::ENDING_TAGS)) {
            $this->setCurrentBuilder(new SimpleBuilder(), $segment->tag());
        }
        $this->currentBuilder->addSegment($segment);

        return $this;
    }

    public function build(): array
    {
        $data = [];

        foreach ($this->builders as $key => $builder) {
            $data[$key] = $builder->build();
        }

        return $data;
    }
}
