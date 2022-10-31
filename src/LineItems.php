<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

class LineItems extends SectionalMessageBuilder
{
    private array $builders;
    private SimpleMessageBuilder $currentBuilder;

    public function addSegment(SegmentInterface $segment): void
    {
        if ($segment->tag() == 'LIN') {
            $this->currentBuilder = new SimpleMessageBuilder();
            $this->builders[$segment->subId()] = $this->currentBuilder;
        }

        $this->currentBuilder->addSegment($segment);
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
