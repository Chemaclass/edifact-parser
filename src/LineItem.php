<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

class LineItem
{
    use HasRetrievableSegments;

    /**
     * @param  array<string, array<string, SegmentInterface>>  $data
     */
    public function __construct(private array $data)
    {
    }

    public function allSegments(): array
    {
        return $this->data;
    }
}
