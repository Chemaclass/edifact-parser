<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;

/** @psalm-immutable */
final class TransactionMessage
{
    /** @var array<string, array<string,SegmentInterface>> */
    private array $groupedSegments;

    /**
     * A message starts with the "UNHMessageHeader" segment until another
     * "UNHMessageHeader" segment appears, then starts another segment and so on.
     *
     * @psalm-pure
     * @psalm-return list<TransactionMessage>
     */
    public static function groupSegmentsByMessage(SegmentInterface...$segments): array
    {
        $messages = [];
        $groupedSegments = [];

        foreach ($segments as $segment) {
            if ($segment instanceof UNHMessageHeader && !empty($groupedSegments)) {
                $messages[] = self::groupSegmentsByName(...$groupedSegments);
                $groupedSegments = [];
            }
            $groupedSegments[] = $segment;
        }

        $messages[] = self::groupSegmentsByName(...$groupedSegments);

        return array_values(
            array_filter($messages, function (self $m) {
                return !empty($m->segmentByName(UNHMessageHeader::class));
            })
        );
    }

    /** @param array<string, array<string,SegmentInterface>> $groupedSegments */
    public function __construct(array $groupedSegments)
    {
        $this->groupedSegments = $groupedSegments;
    }

    public function segmentByName(string $name): array
    {
        return $this->groupedSegments[$name] ?? [];
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
