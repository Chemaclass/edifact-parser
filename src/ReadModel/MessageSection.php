<?php

declare(strict_types=1);

namespace EdifactParser\ReadModel;

use EdifactParser\Segments\SegmentInterface;

/** @psalm-immutable */
final class MessageSection
{
    /** @var array<string, array<string,SegmentInterface>> */
    private array $groupedSegments;

    /** @psalm-pure */
    public static function fromSegments(SegmentInterface...$segments): self
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
