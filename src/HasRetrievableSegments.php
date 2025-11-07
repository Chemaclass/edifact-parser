<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

trait HasRetrievableSegments
{
    /**
     * @return array<string, array<string, SegmentInterface>>
     */
    abstract public function allSegments(): array;

    /**
     * @return array<string,SegmentInterface>
     */
    public function segmentsByTag(string $tag): array
    {
        return $this->allSegments()[$tag] ?? [];
    }

    public function segmentByTagAndSubId(string $tag, string $subId): ?SegmentInterface
    {
        return $this->allSegments()[$tag][$subId] ?? null;
    }

    /**
     * Start a fluent query for segments
     */
    public function query(): SegmentQuery
    {
        $flatSegments = [];
        foreach ($this->allSegments() as $tagSegments) {
            foreach ($tagSegments as $segment) {
                $flatSegments[] = $segment;
            }
        }

        return new SegmentQuery($flatSegments);
    }
}
