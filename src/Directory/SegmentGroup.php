<?php

declare(strict_types=1);

namespace EdifactParser\Directory;

/**
 * A segment group (SG1, SG2, …): an ordered set of segment positions and nested groups,
 * repeated as a unit.
 *
 * The first segment of a group is its **trigger**: it opens a repetition, is mandatory
 * within the group, and may not itself repeat.
 */
final class SegmentGroup
{
    /**
     * @param list<SegmentPosition|SegmentGroup> $parts
     */
    public function __construct(
        private string $id,
        private int $maxRepeat,
        private bool $required,
        private array $parts,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function maxRepeat(): int
    {
        return $this->maxRepeat;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return list<SegmentPosition|SegmentGroup>
     */
    public function parts(): array
    {
        return $this->parts;
    }

    /**
     * The tag that opens this group.
     */
    public function triggerTag(): ?string
    {
        $first = $this->parts[0] ?? null;

        return match (true) {
            $first instanceof SegmentPosition => $first->tag(),
            $first instanceof self => $first->triggerTag(),
            default => null,
        };
    }

    /**
     * A copy with one more part appended — structures are read incrementally.
     */
    public function withPart(SegmentPosition|self $part): self
    {
        return new self($this->id, $this->maxRepeat, $this->required, [...$this->parts, $part]);
    }
}
