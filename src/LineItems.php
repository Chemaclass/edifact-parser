<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

class LineItems implements MessageBuilderInterface
{
    private array $builders;
    private SimpleMessageBuilder $currentBuilder;

    public function addSegment(SegmentInterface $segment): self
    {
        if ($segment->tag() == 'LIN') {
            $this->currentBuilder = new SimpleMessageBuilder();
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
