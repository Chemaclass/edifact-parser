<?php

declare(strict_types=1);

namespace EdifactParser;

use ArrayIterator;
use Countable;
use EdifactParser\MessageDataBuilder\Builder as MessageDataBuilder;
use EdifactParser\Segments\SegmentArray;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNEFunctionalGroupTrailer;
use EdifactParser\Segments\UNGFunctionalGroupHeader;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;
use IteratorAggregate;
use JsonException;
use Traversable;

use function array_values;
use function count;
use function spl_object_id;

/**
 * @implements IteratorAggregate<int, SegmentInterface>
 */
final class TransactionMessage implements Countable, IteratorAggregate
{
    use HasRetrievableSegments;

    /** Tags that live on the interchange itself rather than inside a UNH…UNT message. */
    private const GLOBAL_TAGS = ['UNA' => true, 'UNB' => true, 'UNZ' => true];

    /**
     * Lazily flattened view of {@see self::$segments}; also the fallback for messages
     * built straight from the keyed map. @see self::orderedSegments()
     *
     * @var list<SegmentInterface>|null
     */
    private ?array $ordered = null;

    /** @var array<string, int>|null */
    private ?array $countByTag = null;

    /** @var array<int, ContextSegment>|null Contexts by the object id of the segment that opened them */
    private ?array $contextsBySegmentId = null;

    /**
     * @param  array<string, array<array-key, SegmentInterface>>  $groupedSegments
     * @param  array<array-key, LineItem>  $lineItems
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
        return self::groupSegments($rules, $segments);
    }

    /**
     * Same as {@see self::groupSegmentsByMessage()} for segments you already have
     * together — no argument unpacking, which matters when a single interchange holds
     * hundreds of thousands of segments, and a generator works just as well as an array.
     *
     * @param iterable<SegmentInterface> $segments
     */
    public static function groupSegments(GroupingRules $rules, iterable $segments): ParserResult
    {
        $messages = [];
        $groupedSegments = [];

        $functionalGroups = [];
        $globalSegments = [];
        $openHeader = null;
        $openGroupMessages = [];

        foreach ($segments as $segment) {
            // Collected here rather than in a second pass: an interchange can hold
            // hundreds of thousands of segments and this loop already visits them all.
            if (isset(self::GLOBAL_TAGS[$segment->tag()])) {
                $globalSegments[] = $segment;
            }

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
                $message = self::groupSegmentsByName($rules, $groupedSegments);
                $messages[] = $message;

                if ($openHeader !== null) {
                    $openGroupMessages[] = $message;
                }
            }
        }

        return new ParserResult(
            self::groupSegmentsByName($rules, $globalSegments),
            self::hasUnhSegment(...$messages),
            $functionalGroups,
        );
    }

    /**
     * @return array<array-key, LineItem>
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

    /**
     * The context a segment opened, or null when it opened none. Keyed lookups return
     * the typed segment — `segmentByTagAndSubId('NAD', 'BY')` is a `NADNameAddress`, so
     * its accessors and `instanceof` work — and this is how you get from there to the
     * children that were grouped under it.
     */
    public function contextFor(SegmentInterface $segment): ?ContextSegment
    {
        if ($this->contextsBySegmentId === null) {
            $index = [];
            foreach ($this->contextSegments as $context) {
                // Indexed under both forms, so the lookup works whether you hold the
                // segment from a keyed view or the context from contextSegments().
                $index[spl_object_id($context->segment())] = $context;
                $index[spl_object_id($context)] = $context;
            }
            $this->contextsBySegmentId = $index;
        }

        return $this->contextsBySegmentId[spl_object_id($segment)] ?? null;
    }

    /**
     * The children grouped under a segment, empty when it opened no context.
     *
     * @return list<ContextSegment|SegmentInterface>
     */
    public function childrenOf(SegmentInterface $segment): array
    {
        return $this->contextFor($segment)?->children() ?? [];
    }

    public function lineItemById(string|int $lineItemId): ?LineItem
    {
        return $this->lineItems[$lineItemId] ?? null;
    }

    /**
     * @return array<string, array<array-key, SegmentInterface>>
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
        return new SegmentQuery($this->orderedSegments());
    }

    /**
     * Total number of segments in the message (duplicates included).
     */
    public function count(): int
    {
        return count($this->orderedSegments());
    }

    /**
     * Iterate every segment in original order, duplicates included — so a message can
     * be handed straight to anything taking `iterable<SegmentInterface>`, such as
     * {@see Serializer\EdifactSerializer::serialize()}.
     *
     * @return Traversable<int, SegmentInterface>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->orderedSegments());
    }

    /**
     * How often each tag occurs, in first-seen order. Computed once per message.
     *
     * @return array<string, int>
     */
    public function countByTag(): array
    {
        if ($this->countByTag !== null) {
            return $this->countByTag;
        }

        $counts = [];
        foreach ($this->orderedSegments() as $segment) {
            $tag = $segment->tag();
            $counts[$tag] = ($counts[$tag] ?? 0) + 1;
        }

        return $this->countByTag = $counts;
    }

    /**
     * Whether the message carries at least one segment with the given tag.
     */
    public function has(string $tag): bool
    {
        return isset($this->countByTag()[$tag]);
    }

    /**
     * Get message type from UNH segment (e.g., 'ORDERS', 'INVOIC', 'IFTMIN')
     * Returns null if no UNH segment exists
     */
    public function messageType(): ?string
    {
        $unhSegments = $this->segmentsByTag('UNH');
        if ($unhSegments === []) {
            return null;
        }

        $unhSegment = reset($unhSegments);
        if ($unhSegment instanceof UNHMessageHeader) {
            return $unhSegment->messageType();
        }

        return null;
    }

    public function segmentByTagAndSubId(string $tag, string|int $subId): ?SegmentInterface
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
     * The message as plain data — every segment in order, contexts nested. Handy for
     * logging, snapshot tests and diffing two interchanges.
     *
     * @return array{type: string|null, segments: list<array>}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->messageType(),
            'segments' => SegmentArray::fromSegments($this->orderedSegments()),
        ];
    }

    /**
     * @throws JsonException
     */
    public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT): string
    {
        return json_encode($this->toArray(), $flags);
    }

    /**
     * @return list<SegmentInterface>
     */
    private function orderedSegments(): array
    {
        if ($this->ordered !== null) {
            return $this->ordered;
        }

        if ($this->segments !== []) {
            return $this->ordered = $this->segments;
        }

        // Fallback for a message built directly from the keyed map (no ordered list).
        $flat = [];
        foreach ($this->groupedSegments as $tagSegments) {
            foreach ($tagSegments as $segment) {
                $flat[] = $segment;
            }
        }

        return $this->ordered = $flat;
    }

    /**
     * @return list<TransactionMessage>
     */
    private static function hasUnhSegment(self ...$messages): array
    {
        return array_values(
            array_filter($messages, static fn (self $m) => $m->segmentsByTag('UNH') !== [])
        );
    }

    /**
     * @param list<SegmentInterface> $segments
     */
    private static function groupSegmentsByName(GroupingRules $rules, array $segments): self
    {
        $builder = new MessageDataBuilder($rules);

        foreach ($segments as $segment) {
            $builder->addSegment($segment);
        }

        $lineItems = array_map(
            static fn (array $data) => new LineItem($data),
            $builder->buildLineItemData(),
        );

        return new self(
            $builder->buildSegments(),
            $lineItems,
            (new ContextStackParser($rules))->parseAll($segments),
            $segments,
        );
    }
}
