<?php

declare(strict_types=1);

namespace EdifactParser;

use ArrayIterator;
use Countable;
use EdifactParser\Segments\SegmentInterface;
use IteratorAggregate;
use Traversable;

use function array_fill_keys;
use function array_filter;
use function array_slice;
use function array_values;
use function count;

/**
 * An immutable, ordered and duplicate-preserving view over a set of segments.
 * Every filter returns a new query, so a query can be reused as a base for several
 * refinements.
 *
 * @template T of SegmentInterface
 *
 * @implements IteratorAggregate<int, T>
 */
final class SegmentQuery implements Countable, IteratorAggregate
{
    /**
     * @param list<T> $segments
     */
    public function __construct(private array $segments)
    {
    }

    /**
     * @return self<T>
     */
    public function withTag(string $tag): self
    {
        return $this->where(static fn (SegmentInterface $s) => $s->tag() === $tag);
    }

    /**
     * @param list<string> $tags
     *
     * @return self<T>
     */
    public function withTags(array $tags): self
    {
        $wanted = array_fill_keys($tags, true);

        return $this->where(static fn (SegmentInterface $s) => isset($wanted[$s->tag()]));
    }

    /**
     * The segments whose tag is *not* one of the given ones.
     *
     * @param list<string> $tags
     *
     * @return self<T>
     */
    public function withoutTags(array $tags): self
    {
        $excluded = array_fill_keys($tags, true);

        return $this->where(static fn (SegmentInterface $s) => !isset($excluded[$s->tag()]));
    }

    /**
     * @return self<T>
     */
    public function withSubId(string $subId): self
    {
        return $this->where(static fn (SegmentInterface $s) => $s->subId() === $subId);
    }

    /**
     * @param callable(T): bool $predicate
     *
     * @return self<T>
     */
    public function where(callable $predicate): self
    {
        return new self(array_values(array_filter($this->segments, $predicate)));
    }

    /**
     * Narrows the element type too, so `->ofType(NADNameAddress::class)->first()?->name()`
     * type-checks.
     *
     * @template U of SegmentInterface
     *
     * @param class-string<U> $className
     *
     * @return self<U>
     */
    public function ofType(string $className): self
    {
        return new self(self::instancesOf($className, $this->segments));
    }

    /**
     * @return self<T>
     */
    public function limit(int $limit): self
    {
        return new self(array_slice($this->segments, 0, $limit));
    }

    /**
     * @return self<T>
     */
    public function skip(int $offset): self
    {
        return new self(array_slice($this->segments, $offset));
    }

    /**
     * @return T|null
     */
    public function first(): ?SegmentInterface
    {
        return $this->segments[0] ?? null;
    }

    /**
     * @return T|null
     */
    public function last(): ?SegmentInterface
    {
        $count = count($this->segments);

        return $count > 0 ? $this->segments[$count - 1] : null;
    }

    /**
     * @return list<T>
     */
    public function get(): array
    {
        return $this->segments;
    }

    public function count(): int
    {
        return count($this->segments);
    }

    public function exists(): bool
    {
        return $this->segments !== [];
    }

    public function isEmpty(): bool
    {
        return $this->segments === [];
    }

    /**
     * Map segments to another type
     *
     * @template R
     *
     * @param callable(T): R $mapper
     *
     * @return list<R>
     */
    public function map(callable $mapper): array
    {
        return array_map($mapper, $this->segments);
    }

    /**
     * Fold the segments into a single value — totals, concatenations, custom indexes.
     *
     * @template R
     *
     * @param callable(R, T): R $reducer
     * @param R $initial
     *
     * @return R
     */
    public function reduce(callable $reducer, mixed $initial = null): mixed
    {
        $carry = $initial;

        foreach ($this->segments as $segment) {
            $carry = $reducer($carry, $segment);
        }

        return $carry;
    }

    /**
     * The matching segments bucketed by tag, in first-seen order.
     *
     * @return array<string, list<T>>
     */
    public function groupByTag(): array
    {
        $grouped = [];

        foreach ($this->segments as $segment) {
            $grouped[$segment->tag()][] = $segment;
        }

        return $grouped;
    }

    /**
     * How often each tag occurs among the matching segments, in first-seen order.
     *
     * @return array<string, int>
     */
    public function countByTag(): array
    {
        $counts = [];

        foreach ($this->segments as $segment) {
            $tag = $segment->tag();
            $counts[$tag] = ($counts[$tag] ?? 0) + 1;
        }

        return $counts;
    }

    /**
     * @param callable(T): void $callback
     */
    public function each(callable $callback): void
    {
        foreach ($this->segments as $segment) {
            $callback($segment);
        }
    }

    /**
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->segments);
    }

    /**
     * @template U of SegmentInterface
     *
     * @param class-string<U> $className
     * @param list<SegmentInterface> $segments
     *
     * @return list<U>
     */
    private static function instancesOf(string $className, array $segments): array
    {
        $matching = [];

        foreach ($segments as $segment) {
            if ($segment instanceof $className) {
                $matching[] = $segment;
            }
        }

        return $matching;
    }
}
