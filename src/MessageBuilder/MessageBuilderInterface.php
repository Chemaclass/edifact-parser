<?php

declare(strict_types=1);

namespace EdifactParser\MessageBuilder;

use EdifactParser\Segments\SegmentInterface;

interface MessageBuilderInterface
{
    public function addSegment(SegmentInterface $segment): self;

    public function build(): array;
}
