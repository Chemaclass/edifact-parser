<?php

declare(strict_types=1);

namespace EdifactParser\MessageDataBuilder;

use EdifactParser\Segments\SegmentInterface;

interface BuilderInterface
{
    public function addSegment(SegmentInterface $segment): self;

    public function build(): array;
}
