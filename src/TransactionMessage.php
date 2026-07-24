<?php

declare(strict_types=1);

namespace EdifactParser;

use Countable;
use EdifactParser\MessageDataBuilder\Builder as MessageDataBuilder;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNEFunctionalGroupTrailer;
use EdifactParser\Segments\UNGFunctionalGroupHeader;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;

use function count;
use function in_array;

final class TransactionMessage implements Countable
{
    use HasRetrievableSegments;

    /**
     * @param  array<string, array<string, SegmentInterface>>  $groupedSegments
     * @param  array<int|string, LineItem>  $lineItems
     * @param  list<ContextSegment>  $contextSegments
     * @param  list<SegmentInterface>  $segments  Every segment in original order, duplicates preserved
     */
    public function __construct(
        private array $groupedSegments,
        private array $lineItems = [],
        private array $contextSegments = [],
        private array $segments = [],
    ) {
    }

    /**
     * Every segment of the message in original order, with duplicates preserved
     * (unlike the keyed views, which index by tag+subId). Feed this to the
     * serializer to round-trip a message.
     *
     * @return list<SegmentInterface>
     */
    public function segments(): array
    {
        return $this->segments;
    }

    /**
     * A transaction message starts with the "UNHMessageHeader" segment and finalizes with
     * the "UNTMessageFooter" segment, this process is repeated for each pair of segments.
     */
    public static function groupSegmentsByMessage(GroupingRules $rules, SegmentInterface ...$segments): ParserResult
    {
        $messages = [];
        $groupedSegments = [];

        $functionalGroups = [];
        $openHeader = null;
        $openGroupMessages = [];

        foreach ($segments as $segment) {
            if ($segment instanceof UNGFunctionalGroupHeader) {
                $openHeader = $segment;
                $openGroupMessages = [];
                continue;
            }

            if ($segment instanceof UNEFunctionalGroupTrailer) {
                if ($openHeader !== null) {
                    $functionalGroups[] = new FunctionalGroup($openHeader, $openGroupMessages, $segment);
                    $openHeader = null;
                    $openGroupMessages = [];
                }
                continue;
            }

            if ($segment instanceof UNHMessageHeader) {
                $groupedSegments = [];
            }

            $groupedSegments[] = $segment;

            if ($segment instanceof UNTMessageFooter) {
                $message = self::groupSegmentsByName($rules, ...$groupedSegments);
                $messages[] = $message;

                if ($openHeader !== null) {
                    $openGroupMessages[] = $message;
                }
            }
        }

        return new ParserResult(
            self::filterGlobalSegments($rules, $segments),
            self::hasUnhSegment(...$messages),
            $functionalGroups,
        );
    }

    /**
     * @return array<int|string, LineItem>
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

    /**
     * A duplicate-preserving, ordered query over every segment of the message.
     */
    public function query(): SegmentQuery
    {
        if ($this->segments !== []) {
            return new SegmentQuery($this->segments);
        }

        $flat = [];
        foreach ($this->groupedSegments as $tagSegments) {
            foreach ($tagSegments as $segment) {
                $flat[] = $segment;
            }
        }

        return new SegmentQuery($flat);
    }

    /**
     * Total number of segments in the message (duplicates included).
     */
    public function count(): int
    {
        if ($this->segments !== []) {
            return count($this->segments);
        }

        // Fallback for a message built directly from the keyed map (no ordered list):
        // sum the segments across tags rather than counting tags.
        return array_sum(array_map('count', $this->groupedSegments));
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

    /**
     * @param list<SegmentInterface> $segments
     */
    private static function filterGlobalSegments(GroupingRules $rules, array $segments): self
    {
        $globalMessages = array_filter(
            $segments,
            static fn (SegmentInterface $s) => in_array($s->tag(), ['UNA', 'UNB', 'UNZ'], true)
        );

        return self::groupSegmentsByName($rules, ...$globalMessages);
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

    private static function groupSegmentsByName(GroupingRules $rules, SegmentInterface ...$segments): self
    {
        $builder = new MessageDataBuilder($rules);
        $contextParser = new ContextStackParser($rules);

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

        $lineItems = array_map(static fn (array $data) => new LineItem($data), $lineItemsData);

        return new self(
            $groupedSegments,
            $lineItems,
            $contexts,
            array_values($segments),
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
    }
}
