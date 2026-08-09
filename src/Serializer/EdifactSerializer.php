<?php

declare(strict_types=1);

namespace EdifactParser\Serializer;

use EdifactParser\Segments\SegmentInterface;

use function implode;
use function is_array;
use function strtr;

/**
 * Renders segments back into an EDIFACT string, the inverse of parsing.
 * Separators and the release/escape char follow {@see UnaSeparators}.
 */
final class EdifactSerializer
{
    private UnaSeparators $una;

    private string $component;

    private string $element;

    private string $terminator;

    /**
     * Escape table applied in a single pass — strtr() never re-escapes what it just
     * wrote, so the release char needs no special ordering.
     *
     * @var array<string, string>
     */
    private array $escapeTable;

    public function __construct(?UnaSeparators $una = null)
    {
        $this->una = $una ?? UnaSeparators::default();
        $this->component = $this->una->component();
        $this->element = $this->una->element();
        $this->terminator = $this->una->segmentTerminator();

        $release = $this->una->release();
        $escapeTable = [];
        foreach ($this->una->specialCharacters() as $special) {
            $escapeTable[$special] = $release . $special;
        }
        $this->escapeTable = $escapeTable;
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
            if (!is_array($element)) {
                $parts[] = $this->escape((string) $element);
                continue;
            }

            $components = [];
            foreach ($element as $component) {
                $components[] = $this->escape((string) $component);
            }
            $parts[] = implode($this->component, $components);
        }

        return implode($this->element, $parts) . $this->terminator;
    }

    private function escape(string $value): string
    {
        return strtr($value, $this->escapeTable);
    }
}
