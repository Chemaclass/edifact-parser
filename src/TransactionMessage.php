<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\MessageDataBuilder\Builder as MessageDataBuilder;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;

/** @psalm-immutable */
final class TransactionMessage
{
    /** @var array<string, array<string,SegmentInterface>> */
    private array $groupedSegments;

    /**
     * @param  array<string, array<string,SegmentInterface>>  $groupedSegments
     */
    public function __construct(array $groupedSegments)
    {
        $this->groupedSegments = $groupedSegments;
    }

    /**
     * A transaction message starts with the "UNHMessageHeader" segment and finalizes with
     * the "UNTMessageFooter" segment, this process is repeated for each pair of segments.
     *
     * @return list<TransactionMessage>
     */
    public static function groupSegmentsByMessage(SegmentInterface ...$segments): array
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

        return self::hasUnhSegment(...$messages);
    }

    /**
     * @return array<string,SegmentInterface>
     */
    public function segmentsByTag(string $tag): array
    {
        return $this->groupedSegments[$tag] ?? [];
    }

    public function segmentByTagAndSubId(string $tag, string $subId): ?SegmentInterface
    {
        return $this->groupedSegments[$tag][$subId] ?? null;
    }

    /**
     * @return list<TransactionMessage>
     */
    private static function hasUnhSegment(self ...$messages): array
    {
        return array_values(
            array_filter($messages, static function (self $m) {
                return !empty($m->segmentsByTag('UNH'));
            })
        );
    }

    private static function groupSegmentsByName(SegmentInterface ...$segments): self
    {
        $builder = new MessageDataBuilder();

        foreach ($segments as $segment) {
            $builder->addSegment($segment);
        }

        return new self($builder->build());
    }
}
