<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

final class ContextSegment implements SegmentInterface
{
    /**
     * @param list<ContextSegment|SegmentInterface> $children
     */
    public function __construct(
        private SegmentInterface $segment,
        private array $children = [],
    ) {
    }

    public function segment(): SegmentInterface
    {
        return $this->segment;
    }

    public function tag(): string
    {
        return $this->segment->tag();
    }

    public function subId(): string
    {
        return $this->segment->subId();
    }

    public function parsedSubId(): array
    {
        return $this->segment->parsedSubId();
    }

    public function rawValues(): array
    {
        return $this->segment->rawValues();
    }

    /**
     * @return list<ContextSegment|SegmentInterface>
     */
    public function children(): array
    {
        return $this->children;
    }

    public function addChild(self|SegmentInterface $child): void
    {
        $this->children[] = $child;
    }
}
