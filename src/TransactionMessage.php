<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

/** @psalmphp-immutable */
final class TransactionMessage
{
    /**
     * First string: segment key
     * Second key: sub segment key.
     *
     * @psalm-var array<string, array<string,SegmentInterface>>
     */
    private array $segments = [];

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

    public function segments(): array
    {
        return $this->segments;
    }
}
