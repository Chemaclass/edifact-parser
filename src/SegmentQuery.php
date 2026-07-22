<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

use function array_filter;
use function array_slice;
use function array_values;
use function count;
use function in_array;

final class SegmentQuery
{
    /**
     * @param list<SegmentInterface> $segments
     */
    public function __construct(private array $segments)
    {
    }

    public function withTag(string $tag): self
    {
        return new self(array_values(array_filter(
            $this->segments,
            static fn (SegmentInterface $s) => $s->tag() === $tag
        )));
    }

    /**
     * @param list<string> $tags
     */
    public function withTags(array $tags): self
    {
        return new self(array_values(array_filter(
            $this->segments,
            static fn (SegmentInterface $s) => in_array($s->tag(), $tags, true)
        )));
    }

    public function withSubId(string $subId): self
    {
        return new self(array_values(array_filter(
            $this->segments,
            static fn (SegmentInterface $s) => $s->subId() === $subId
        )));
    }

    /**
     * @param callable(SegmentInterface): bool $predicate
     */
    public function where(callable $predicate): self
    {
        return new self(array_values(array_filter($this->segments, $predicate)));
    }

    /**
     * @template T of SegmentInterface
     *
     * @param class-string<T> $className
     */
    public function ofType(string $className): self
    {
        return new self(array_values(array_filter(
            $this->segments,
            static fn (SegmentInterface $s) => $s instanceof $className
        )));
    }

    public function limit(int $limit): self
    {
        return new self(array_slice($this->segments, 0, $limit));
    }

    public function skip(int $offset): self
    {
        return new self(array_slice($this->segments, $offset));
    }

    public function first(): ?SegmentInterface
    {
        return $this->segments[0] ?? null;
    }

    public function last(): ?SegmentInterface
    {
        $count = count($this->segments);
        return $count > 0 ? $this->segments[$count - 1] : null;
    }

    /**
     * @return list<SegmentInterface>
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
        return count($this->segments) > 0;
    }

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
     * @param callable(SegmentInterface): void $callback
     */
    public function each(callable $callback): void
    {
        foreach ($this->segments as $segment) {
            $callback($segment);
        }
    }
}
