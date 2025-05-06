<?php

declare(strict_types=1);

namespace EdifactParser\MessageDataBuilder;

use EdifactParser\Segments\SegmentInterface;

class DetailsSectionBuilder implements BuilderInterface
{
    use MultipleBuilderWrapper;

    private const LINE_ITEM_TAG = 'LIN';

    public function addSegment(SegmentInterface $segment): self
    {
        // Only handle LIN and its related segments
        if ($segment->tag() === self::LINE_ITEM_TAG) {
            $this->setCurrentBuilder(new SimpleBuilder(), $segment->subId());
            $this->currentBuilder->addSegment($segment);
        } elseif ($this->currentBuilder !== null) {
            // Add other segments only if we're inside a line item
            $this->currentBuilder->addSegment($segment);
        }

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
