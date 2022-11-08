<?php

declare(strict_types=1);

namespace EdifactParser\MessageDataBuilder;

use EdifactParser\Segments\SegmentInterface;

class DetailsSectionBuilder implements BuilderInterface
{
    use MultipleBuilderWrapper;

    public function addSegment(SegmentInterface $segment): self
    {
        if ($segment->tag() == 'LIN') {
            $this->setCurrentBuilder(new SimpleBuilder(), $segment->subId());
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
