<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;

/** @psalm-immutable */
final class TransactionMessage
{
    /** @var array<string, array<string,SegmentInterface>> */
    private array $groupedSegments;

    /**
     * A transaction message starts with the "UNHMessageHeader" segment and finalizes with
     * the "UNTMessageFooter" segment, this process is repeated for each pair of segments.
     *
     * @psalm-pure
     * @psalm-return list<TransactionMessage>
     */
    public static function groupSegmentsByMessage(SegmentInterface...$segments): array
    {
        $messages = [];
        $groupedSegments = [];

        foreach ($segments as $segment) {
            if ($segment instanceof UNHMessageHeader) {
                $groupedSegments = [];
            }

            $groupedSegments[] = $segment;

            if ($segment instanceof UNTMessageFooter) {
                $messages[] = static::groupSegmentsByName(...$groupedSegments);
            }
        }

        return static::hasUnhSegment(...$messages);
    }

    /** @param array<string, array<string,SegmentInterface>> $groupedSegments */
    public function __construct(array $groupedSegments)
    {
        $this->groupedSegments = $groupedSegments;
    }

    /** @return array|array<string, SegmentInterface> */
    public function segmentsByTag(string $tag): array
    {
        return $this->groupedSegments[$tag] ?? [];
    }

    /** @return ?SegmentInterface */
    public function segmentByTagAndSubId(string $tag, string $subId): ?SegmentInterface
    {
        return $this->groupedSegments[$tag][$subId] ?? null;
    }

    /**
     * @psalm-pure
     * @psalm-return list<TransactionMessage>
     */
    private static function hasUnhSegment(self...$messages): array
    {
        return array_values(
            array_filter($messages, static function (self $m) {
                return !empty($m->segmentsByTag(UNHMessageHeader::class));
            })
        );
    }

    /** @psalm-pure */
    private static function groupSegmentsByName(SegmentInterface...$segments): self
    {
        $return = [];

        foreach ($segments as $s) {
            $return[$s->tag()] ??= [];
            $return[$s->tag()][$s->subId()] = $s;
        }

        return new self($return);
    }
}
