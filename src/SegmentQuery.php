<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

use function array_filter;
use function array_slice;
use function array_values;
use function count;
use function in_array;

/**
 * Fluent query builder for filtering segments
 */
final class SegmentQuery
{
    /**
     * @param list<SegmentInterface> $segments
     */
    public function __construct(private array $segments)
    {
    }

    /**
     * Filter segments by tag
     */
    public function withTag(string $tag): self
    {
        return new self(array_values(array_filter(
            $this->segments,
            static fn (SegmentInterface $s) => $s->tag() === $tag
        )));
    }

    /**
     * Filter segments by multiple tags
     *
     * @param list<string> $tags
     */
    public function withTags(array $tags): self
    {
        return new self(array_values(array_filter(
            $this->segments,
            static fn (SegmentInterface $s) => in_array($s->tag(), $tags, true)
        )));
    }

    /**
     * Filter segments by subId
     */
    public function withSubId(string $subId): self
    {
        return new self(array_values(array_filter(
            $this->segments,
            static fn (SegmentInterface $s) => $s->subId() === $subId
        )));
    }

    /**
     * Filter segments by custom predicate
     *
     * @param callable(SegmentInterface): bool $predicate
     */
    public function where(callable $predicate): self
    {
        return new self(array_values(array_filter($this->segments, $predicate)));
    }

    /**
     * Filter segments by type (class)
     *
     * @template T of SegmentInterface
     *
     * @param class-string<T> $className
     *
     * @return self
     */
    public function ofType(string $className): self
    {
        return new self(array_values(array_filter(
            $this->segments,
            static fn (SegmentInterface $s) => $s instanceof $className
        )));
    }

    /**
     * Limit the number of results
     */
    public function limit(int $limit): self
    {
        return new self(array_slice($this->segments, 0, $limit));
    }

    /**
     * Skip a number of results
     */
    public function skip(int $offset): self
    {
        return new self(array_slice($this->segments, $offset));
    }

    /**
     * Get the first segment or null
     */
    public function first(): ?SegmentInterface
    {
        return $this->segments[0] ?? null;
    }

    /**
     * Get the last segment or null
     */
    public function last(): ?SegmentInterface
    {
        $count = count($this->segments);
        return $count > 0 ? $this->segments[$count - 1] : null;
    }

    /**
     * Get all matching segments
     *
     * @return list<SegmentInterface>
     */
    public function get(): array
    {
        return $this->segments;
    }

    /**
     * Get count of matching segments
     */
    public function count(): int
    {
        return count($this->segments);
    }

    /**
     * Check if any segments match
     */
    public function exists(): bool
    {
        return count($this->segments) > 0;
    }

    /**
     * Check if no segments match
     */
    public function isEmpty(): bool
    {
        return count($this->segments) === 0;
    }

    /**
     * Map segments to another type
     *
     * @template T
     *
     * @param callable(SegmentInterface): T $mapper
     *
     * @return list<T>
     */
    public function map(callable $mapper): array
    {
        return array_map($mapper, $this->segments);
    }

    /**
     * Execute callback for each segment
     *
     * @param callable(SegmentInterface): void $callback
     */
    public function each(callable $callback): void
    {
        foreach ($this->segments as $segment) {
            $callback($segment);
        }
    }
}
