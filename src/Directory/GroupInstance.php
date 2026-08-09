<?php

declare(strict_types=1);

namespace EdifactParser\Directory;

use ArrayIterator;
use Countable;
use EdifactParser\Segments\SegmentInterface;
use IteratorAggregate;
use Traversable;

use function count;

/**
 * One occurrence of a segment group in a parsed message: the segments that sit directly in
 * it, plus any nested group occurrences.
 *
 * @implements IteratorAggregate<int, SegmentInterface>
 */
final class GroupInstance implements Countable, IteratorAggregate
{
    /**
     * @param list<SegmentInterface> $segments
     * @param list<GroupInstance> $children
     */
    public function __construct(
        private string $id,
        private int $occurrence,
        private array $segments,
        private array $children,
    ) {
    }

    /**
     * The group id from the directory, e.g. 'SG2'.
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Zero-based index of this repetition of the group.
     */
    public function occurrence(): int
    {
        return $this->occurrence;
    }

    /**
     * Segments directly inside this group, excluding those in nested groups.
     *
     * @return list<SegmentInterface>
     */
    public function segments(): array
    {
        return $this->segments;
    }

    /**
     * @return list<GroupInstance>
     */
    public function children(): array
    {
        return $this->children;
    }

    /**
     * Nested occurrences of one group id.
     *
     * @return list<GroupInstance>
     */
    public function childrenOfGroup(string $id): array
    {
        $matching = [];

        foreach ($this->children as $child) {
            if ($child->id() === $id) {
                $matching[] = $child;
            }
        }

        return $matching;
    }

    public function segmentByTag(string $tag): ?SegmentInterface
    {
        foreach ($this->segments as $segment) {
            if ($segment->tag() === $tag) {
                return $segment;
            }
        }

        return null;
    }

    public function count(): int
    {
        return count($this->segments);
    }

    /**
     * @return Traversable<int, SegmentInterface>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->segments);
    }

    /**
     * @return array{group: string, occurrence: int, segments: list<array>, children: list<array>}
     */
    public function toArray(): array
    {
        return [
            'group' => $this->id,
            'occurrence' => $this->occurrence,
            'segments' => \EdifactParser\Segments\SegmentArray::fromSegments($this->segments),
            'children' => array_map(static fn (self $child): array => $child->toArray(), $this->children),
        ];
    }
}
