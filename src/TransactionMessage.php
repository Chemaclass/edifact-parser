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
     * @psalm-pure
     * @psalm-return list<TransactionMessage>
     */
    public static function groupSegmentsByMessage(SegmentInterface...$segments): array
    {
        $messages = [];
        $segmentsGroup = [];

        foreach ($segments as $segment) {
            if ($segment instanceof UNHMessageHeader) {
                if ($segmentsGroup) {
                    $messages[] = self::withSegments(...$segmentsGroup);
                }
                $segmentsGroup = [];
            }
            $segmentsGroup[] = $segment;
        }

        $messages[] = self::withSegments(...$segmentsGroup);

        return $messages;
    }

    /** @psalm-pure */
    public static function withSegments(SegmentInterface...$segments): self
    {
        $return = [];

        foreach ($segments as $s) {
            $return[$s->name()] ??= [];
            $return[$s->name()][$s->subSegmentKey()] = $s;
        }

        return new self($return);
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
}
