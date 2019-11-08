<?php

declare(strict_types=1);

namespace App\EdifactParser;

use App\EdifactParser\Segments\SegmentInterface;

final class TransactionMessage
{
    /**
     * First string: segment key
     * Second key: sub segment key.
     *
     * @var array<string, array<string,SegmentInterface>>
     */
    private $segments = [];

    public function __construct(array $segments = [])
    {
        foreach ($segments as $segment) {
            $this->addSegment($segment);
        }
    }

    public function addSegment(SegmentInterface $segment): void
    {
        $name = $segment->name();

        if (!isset($this->segments[$name])) {
            $this->segments[$name] = [];
        }

        $this->segments[$name][$segment->subSegmentKey()] = $segment;
    }

    /** @return array<string, array<string,SegmentInterface>> */
    public function segments(): array
    {
        return $this->segments;
    }
}
