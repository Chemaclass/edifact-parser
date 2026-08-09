<?php

declare(strict_types=1);

namespace EdifactParser;

use ArrayIterator;
use Countable;
use EdifactParser\Segments\SegmentArray;
use EdifactParser\Segments\SegmentInterface;
use IteratorAggregate;
use JsonException;
use Traversable;

use function count;

/**
 * A segment plus the segments that belong to it (a NAD with its CTA/COM, a LIN with
 * its QTY/PRI, …). It stands in for the segment it decorates everywhere a
 * {@see SegmentInterface} is expected, so `tag()`, `subId()` and `rawValues()` read
 * through to the wrapped segment.
 *
 * @implements IteratorAggregate<int, ContextSegment|SegmentInterface>
 */
final class ContextSegment implements SegmentInterface, Countable, IteratorAggregate
{
    /**
     * @param list<ContextSegment|SegmentInterface> $children
     */
    public function __construct(
        private SegmentInterface $segment,
        private array $children = [],
    ) {
    }

    public function segment(): SegmentInterface
    {
        return $this->segment;
    }

    public function tag(): string
    {
        return $this->segment->tag();
    }

    public function subId(): string
    {
        return $this->segment->subId();
    }

    public function parsedSubId(): array
    {
        return $this->segment->parsedSubId();
    }

    public function rawValues(): array
    {
        return $this->segment->rawValues();
    }

    /**
     * @return list<ContextSegment|SegmentInterface>
     */
    public function children(): array
    {
        return $this->children;
    }

    /**
     * The children carried under the given tag, in document order.
     *
     * @return list<ContextSegment|SegmentInterface>
     */
    public function childrenByTag(string $tag): array
    {
        $matching = [];

        foreach ($this->children as $child) {
            if ($child->tag() === $tag) {
                $matching[] = $child;
            }
        }

        return $matching;
    }

    /**
     * The first child with the given tag, or null when the context has none.
     */
    public function childByTag(string $tag): self|SegmentInterface|null
    {
        foreach ($this->children as $child) {
            if ($child->tag() === $tag) {
                return $child;
            }
        }

        return null;
    }

    public function hasChildren(): bool
    {
        return $this->children !== [];
    }

    public function addChild(self|SegmentInterface $child): void
    {
        $this->children[] = $child;
    }

    public function count(): int
    {
        return count($this->children);
    }

    /**
     * @return Traversable<int, ContextSegment|SegmentInterface>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->children);
    }

    /**
     * The wrapped segment plus its children, nested — same shape as
     * {@see Segments\AbstractSegment::toArray()} with an extra `children` key.
     *
     * @return array{tag: string, subId: string, rawValues: array, children?: list<array>}
     */
    public function toArray(): array
    {
        return SegmentArray::fromSegment($this);
    }

    /**
     * @throws JsonException
     */
    public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT): string
    {
        return json_encode($this->toArray(), $flags);
    }
}
