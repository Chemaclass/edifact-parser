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
 * @implements IteratorAggregate<int, TransactionMessage>
 */
final class ParserResult implements Countable, IteratorAggregate
{
    use HasRetrievableSegments;

    /** @var array<string, array<array-key, SegmentInterface>>|null */
    private ?array $mergedSegments = null;

    /**
     * @param list<TransactionMessage> $transactionMessages
     * @param list<FunctionalGroup> $functionalGroups
     */
    public function __construct(
        private TransactionMessage $globalSegments,
        private array $transactionMessages,
        private array $functionalGroups = [],
    ) {
    }

    public function globalSegments(): TransactionMessage
    {
        return $this->globalSegments;
    }

    /**
     * @return list<TransactionMessage>
     */
    public function transactionMessages(): array
    {
        return $this->transactionMessages;
    }

    /**
     * The first message of the interchange, or null when it carries none.
     */
    public function firstMessage(): ?TransactionMessage
    {
        return $this->transactionMessages[0] ?? null;
    }

    /**
     * Only the messages of the given type ('ORDERS', 'INVOIC', …) — an interchange
     * may mix several.
     *
     * @return list<TransactionMessage>
     */
    public function messagesOfType(string $messageType): array
    {
        $messages = [];

        foreach ($this->transactionMessages as $message) {
            if ($message->messageType() === $messageType) {
                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * UNG...UNE functional groups, when the interchange uses them (otherwise empty;
     * messages are still available flat via transactionMessages()).
     *
     * @return list<FunctionalGroup>
     */
    public function functionalGroups(): array
    {
        return $this->functionalGroups;
    }

    /**
     * Number of transaction messages in the interchange.
     */
    public function count(): int
    {
        return count($this->transactionMessages);
    }

    /**
     * @return Traversable<int, TransactionMessage>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->transactionMessages);
    }

    /**
     * Combine global and transactional segments in one array. Computed once.
     *
     * @return array<string, array<array-key, SegmentInterface>>
     */
    public function allSegments(): array
    {
        if ($this->mergedSegments !== null) {
            return $this->mergedSegments;
        }

        $all = $this->globalSegments->allSegments();

        foreach ($this->transactionMessages as $message) {
            foreach ($message->allSegments() as $tag => $segments) {
                foreach ($segments as $subId => $segment) {
                    $all[$tag][$subId] = $segment;
                }
            }
        }

        return $this->mergedSegments = $all;
    }
}
