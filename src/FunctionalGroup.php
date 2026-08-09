<?php

declare(strict_types=1);

namespace EdifactParser;

use ArrayIterator;
use Countable;
use EdifactParser\Segments\UNEFunctionalGroupTrailer;
use EdifactParser\Segments\UNGFunctionalGroupHeader;
use IteratorAggregate;
use Traversable;

use function count;

/**
 * A UNG...UNE functional group: a set of messages of the same type wrapped by a
 * UNG header and UNE trailer. Optional in EDIFACT — many interchanges send
 * messages directly under the interchange (UNB) with no functional group.
 *
 * @implements IteratorAggregate<int, TransactionMessage>
 */
final class FunctionalGroup implements Countable, IteratorAggregate
{
    /**
     * @param list<TransactionMessage> $messages
     */
    public function __construct(
        private UNGFunctionalGroupHeader $header,
        private array $messages,
        private ?UNEFunctionalGroupTrailer $trailer = null,
    ) {
    }

    public function header(): UNGFunctionalGroupHeader
    {
        return $this->header;
    }

    public function trailer(): ?UNEFunctionalGroupTrailer
    {
        return $this->trailer;
    }

    public function messageType(): string
    {
        return $this->header->messageType();
    }

    /**
     * @return list<TransactionMessage>
     */
    public function messages(): array
    {
        return $this->messages;
    }

    /**
     * Number of messages inside the group.
     */
    public function count(): int
    {
        return count($this->messages);
    }

    /**
     * @return Traversable<int, TransactionMessage>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->messages);
    }
}
