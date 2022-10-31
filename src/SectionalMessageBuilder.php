<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

abstract class SectionalMessageBuilder
{
    abstract public function addSegment(SegmentInterface $segment): void;

    abstract public function build(): array;
}
