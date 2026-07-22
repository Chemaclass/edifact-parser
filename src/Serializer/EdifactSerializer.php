<?php

declare(strict_types=1);

namespace EdifactParser\Serializer;

use EdifactParser\Segments\SegmentInterface;

use function array_map;
use function implode;
use function is_array;

/**
 * Renders segments back into an EDIFACT string, the inverse of parsing.
 * Separators and the release/escape char follow {@see UnaSeparators}.
 */
final class EdifactSerializer
{
    private UnaSeparators $una;

    public function __construct(?UnaSeparators $una = null)
    {
        $this->una = $una ?? UnaSeparators::default();
    }

    /**
     * @param iterable<SegmentInterface> $segments
     */
    public function serialize(iterable $segments, bool $includeUna = false): string
    {
        $lines = [];

        if ($includeUna) {
            $lines[] = $this->una->toUnaSegment();
        }

        foreach ($segments as $segment) {
            $lines[] = $this->serializeSegment($segment);
        }

        return implode("\n", $lines);
    }

    public function serializeSegment(SegmentInterface $segment): string
    {
        $parts = [];

        foreach ($segment->rawValues() as $element) {
            $parts[] = is_array($element)
                ? implode($this->una->component(), array_map(fn ($value): string => $this->escape((string) $value), $element))
                : $this->escape((string) $element);
        }

        return implode($this->una->element(), $parts) . $this->una->segmentTerminator();
    }

    private function escape(string $value): string
    {
        $release = $this->una->release();

        foreach ($this->una->specialCharacters() as $special) {
            $value = str_replace($special, $release . $special, $value);
        }

        return $value;
    }
}
