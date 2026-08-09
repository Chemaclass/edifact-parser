<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use EdifactParser\ContextSegment;

/**
 * Converts segments into plain arrays — the single source of truth behind every
 * `toArray()`/`toJson()` in the library, so a segment, a context segment and a whole
 * message all describe themselves the same way.
 */
final class SegmentArray
{
    /**
     * @codeCoverageIgnore Prevents instantiation of this utility holder
     */
    private function __construct()
    {
    }

    /**
     * @return array{tag: string, subId: string, rawValues: array, children?: list<array>}
     */
    public static function fromSegment(SegmentInterface $segment): array
    {
        $data = [
            'tag' => $segment->tag(),
            'subId' => $segment->subId(),
            'rawValues' => $segment->rawValues(),
        ];

        if ($segment instanceof ContextSegment) {
            $data['children'] = self::fromSegments($segment->children());
        }

        return $data;
    }

    /**
     * @param iterable<SegmentInterface> $segments
     *
     * @return list<array{tag: string, subId: string, rawValues: array, children?: list<array>}>
     */
    public static function fromSegments(iterable $segments): array
    {
        $data = [];

        foreach ($segments as $segment) {
            $data[] = self::fromSegment($segment);
        }

        return $data;
    }
}
