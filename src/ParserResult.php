<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

final class ParserResult
{
    use HasRetrievableSegments;

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
     * Combine global and transactional segments in one array.
     *
     * @return array<string, array<string, SegmentInterface>>
     */
    public function allSegments(): array
    {
        $all = $this->globalSegments->allSegments();

        foreach ($this->transactionMessages as $message) {
            foreach ($message->allSegments() as $tag => $segments) {
                if (!isset($all[$tag])) {
                    $all[$tag] = [];
                }
                foreach ($segments as $subId => $segment) {
                    $all[$tag][$subId] = $segment;
                }
            }
        }

        return $all;
    }
}
