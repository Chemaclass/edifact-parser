<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

class Normal extends SectionalMessageBuilder
{
    private SimpleMessageBuilder $builder;

    public function __construct()
    {
        $this->builder = new SimpleMessageBuilder();
    }

    public function addSegment(SegmentInterface $segment): void
    {
        $this->builder->addSegment($segment);
    }

    public function build(): array
    {
        return $this->builder->build();
    }
}
