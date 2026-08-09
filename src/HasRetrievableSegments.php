<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

trait HasRetrievableSegments
{
    /**
     * Segments keyed by tag and then by subId. A subId that looks like an integer
     * ('1', '21') becomes an int key — PHP normalizes those — hence `array-key`.
     *
     * @return array<string, array<array-key, SegmentInterface>>
     */
    abstract public function allSegments(): array;

    /**
     * @return array<array-key, SegmentInterface>
     */
    public function segmentsByTag(string $tag): array
    {
        return $this->allSegments()[$tag] ?? [];
    }

    public function segmentByTagAndSubId(string $tag, string|int $subId): ?SegmentInterface
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
