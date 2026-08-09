<?php

declare(strict_types=1);

namespace EdifactParser;

use ArrayIterator;
use Countable;
use EdifactParser\Segments\SegmentInterface;
use IteratorAggregate;
use Traversable;

use function count;

/**
 * One LIN block: the line-item segment together with everything that belongs to it
 * (QTY, PRI, IMD, …), keyed by tag and subId.
 *
 * @implements IteratorAggregate<int, SegmentInterface>
 */
final class LineItem implements Countable, IteratorAggregate
{
    use HasRetrievableSegments;

    /**
     * @param  array<string, array<array-key, SegmentInterface>>  $data
     */
    public function __construct(private array $data)
    {
    }

    public function allSegments(): array
    {
        return $this->data;
    }

    /**
     * Total number of segments in this line item.
     */
    public function count(): int
    {
        return count($this->query()->get());
    }

    /**
     * @return Traversable<int, SegmentInterface>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->query()->get());
    }
}
