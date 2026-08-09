<?php

declare(strict_types=1);

namespace EdifactParser\Directory;

use function count;

/**
 * What a directory says a segment looks like: its ordered parts, each either a simple
 * {@see DataElement} or a {@see Composite}. Position 0 of a parsed segment is the tag, so
 * part 0 here corresponds to `rawValues()[1]`.
 */
final class SegmentDefinition
{
    /**
     * @param list<Composite|DataElement> $parts
     */
    public function __construct(
        private string $tag,
        private string $name,
        private array $parts,
    ) {
    }

    public function tag(): string
    {
        return $this->tag;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return list<Composite|DataElement>
     */
    public function parts(): array
    {
        return $this->parts;
    }

    public function partAt(int $index): Composite|DataElement|null
    {
        return $this->parts[$index] ?? null;
    }

    public function partCount(): int
    {
        return count($this->parts);
    }
}
