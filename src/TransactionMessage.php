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
     * Get message type from UNH segment (e.g., 'ORDERS', 'INVOIC', 'IFTMIN')
     * Returns null if no UNH segment exists
     */
    public function messageType(): ?string
    {
        $unhSegments = $this->segmentsByTag('UNH');
        if (empty($unhSegments)) {
            return null;
        }

        $unhSegment = reset($unhSegments);
        if ($unhSegment instanceof UNHMessageHeader) {
            return $unhSegment->messageType();
        }

        return null;
    }

    public function segmentByTagAndSubId(string $tag, string $subId): ?SegmentInterface
    {
        $segment = $this->groupedSegments[$tag][$subId] ?? null;
        if ($segment !== null) {
            return $segment;
        }

        foreach ($this->lineItems as $lineItem) {
            $segment = $lineItem->segmentByTagAndSubId($tag, $subId);
            if ($segment !== null) {
                return $segment;
            }
        }

        return null;
    }

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
        $groupedSegments = $builder->buildSegments();
        $lineItemsData = [];
        foreach ($builder->buildLineItems() as $key => $lineItem) {
            $lineItemsData[$key] = $lineItem->allSegments();
        }

        $contexts = $contextParser->parse(...$segments);

        foreach ($contexts as $context) {
            self::applyContext($context, $groupedSegments, $lineItemsData);
        }

        $lineItems = array_map(static fn ($data) => new LineItem($data), $lineItemsData);

        return new self(
            $groupedSegments,
            $lineItems,
            $contexts,
        );
    }

    /**
     * @param array<string, array<string, SegmentInterface>> $grouped
     * @param array<string|int, array<string, array<string, SegmentInterface>>> $lineItems
     */
    private static function applyContext(ContextSegment $context, array &$grouped, array &$lineItems): void
    {
        $segment = $context->segment();
        $tag = $segment->tag();
        $subId = $segment->subId();

        if (isset($grouped[$tag][$subId]) && $grouped[$tag][$subId] === $segment) {
            $grouped[$tag][$subId] = $context;
        }

        foreach ($lineItems as &$segments) {
            if (isset($segments[$tag][$subId]) && $segments[$tag][$subId] === $segment) {
                $segments[$tag][$subId] = $context;
                break;
            }
        }
        unset($segments);

        foreach ($context->children() as $child) {
            if ($child instanceof ContextSegment) {
                self::applyContext($child, $grouped, $lineItems);
            }
        }
    }
}
