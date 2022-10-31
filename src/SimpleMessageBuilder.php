<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

class SimpleMessageBuilder
{
    protected array $data;

    public function addSegment(SegmentInterface $segment): self
    {
        $this->data[$segment->tag()] ??= [];
        $this->data[$segment->tag()][$segment->subId()] = $segment;

        return $this;
    }

    public function build(): array
    {
        return $this->data;
    }
}
