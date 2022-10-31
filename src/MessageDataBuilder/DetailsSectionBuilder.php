<?php

declare(strict_types=1);

namespace EdifactParser\MessageDataBuilder;

use EdifactParser\Segments\SegmentInterface;

class DetailsSectionBuilder implements BuilderInterface
{
    private array $builders;
    private SimpleBuilder $currentBuilder;

    public function addSegment(SegmentInterface $segment): self
    {
        if ($segment->tag() == 'LIN') {
            $this->currentBuilder = new SimpleBuilder();
            $this->builders[$segment->subId()] = $this->currentBuilder;
        }

        $this->currentBuilder->addSegment($segment);

        return $this;
    }

    public function build(): array
    {
        $data = [];

        foreach ($this->builders as $key => $builder) {
            $data['LIN'][$key] = $builder->build();
        }

        return $data;
    }
}
