<?php

declare(strict_types=1);

namespace EdifactParser;

use Countable;
use EdifactParser\MessageDataBuilder\Builder as MessageDataBuilder;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;

use function count;
use function in_array;

/** @psalm-immutable */
final class TransactionMessage implements Countable
{
    use HasRetrievableSegments;

    /**
     * @param  array<string, array<string, SegmentInterface>>  $groupedSegments
     * @param  array<string, LineItem>  $lineItems
     * @param  list<ContextSegment>  $contextSegments
     */
    public function __construct(
        private array $groupedSegments,
        private array $lineItems = [],
        private array $contextSegments = [],
    ) {
    }

    /**
     * A transaction message starts with the "UNHMessageHeader" segment and finalizes with
     * the "UNTMessageFooter" segment, this process is repeated for each pair of segments.
     */
    public static function groupSegmentsByMessage(SegmentInterface ...$segments): ParserResult
    {
        $messages = [];
        $groupedSegments = [];

        foreach ($segments as $segment) {
            if ($segment instanceof UNHMessageHeader) {
                $groupedSegments = [];
            }

            $groupedSegments[] = $segment;

            if ($segment instanceof UNTMessageFooter) {
                $messages[] = self::groupSegmentsByName(...$groupedSegments);
            }
        }

        return new ParserResult(
            self::filterGlobalSegments($segments),
            self::hasUnhSegment(...$messages)
        );
    }

    /**
     * @return array<string, LineItem>
     */
    public function lineItems(): array
    {
        return $this->lineItems;
    }

    /**
     * @return list<ContextSegment>
     */
    public function contextSegments(): array
    {
        return $this->contextSegments;
    }

    public function lineItemById(string|int $lineItemId): ?LineItem
    {
        return $this->lineItems[(string) $lineItemId] ?? null;
    }

    /**
     * @return array<string, array<string,SegmentInterface>>
     */
    public function allSegments(): array
    {
        return $this->groupedSegments;
    }

    public function count(): int
    {
        return count($this->groupedSegments);
    }

    /**
     * @return TransactionMessage
     */
    private static function filterGlobalSegments(array $segments): self
    {
        $globalMessages = array_filter(
            $segments,
            static fn (SegmentInterface $s) => in_array($s->tag(), ['UNA', 'UNB', 'UNZ'])
        );

        return self::groupSegmentsByName(...$globalMessages);
    }

    /**
     * @return list<TransactionMessage>
     */
    private static function hasUnhSegment(self ...$messages): array
    {
        return array_values(
            array_filter($messages, static fn (self $m) => !empty($m->segmentsByTag('UNH')))
        );
    }

    private static function groupSegmentsByName(SegmentInterface ...$segments): self
    {
        $builder = new MessageDataBuilder();
        $contextParser = new ContextStackParser();

        foreach ($segments as $segment) {
            $builder->addSegment($segment);
        }

        $contexts = $contextParser->parse(...$segments);

        return new self(
            $builder->buildSegments(),
            $builder->buildLineItems(),
            $contexts,
        );
    }
}
