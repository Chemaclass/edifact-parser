<?php

declare(strict_types=1);

namespace EdifactParser\Diff;

use EdifactParser\Segments\SegmentArray;
use EdifactParser\Segments\SegmentInterface;

use Stringable;

use function sprintf;

/**
 * One difference between two interchanges: a segment present on only one side, or one
 * present on both whose values changed.
 */
final class Difference implements Stringable
{
    public const ADDED = 'added';

    public const REMOVED = 'removed';

    public const CHANGED = 'changed';

    private function __construct(
        private string $kind,
        private int $messageIndex,
        private string $tag,
        private string $subId,
        private ?SegmentInterface $before,
        private ?SegmentInterface $after,
    ) {
    }

    /**
     * A one-line rendering in the shape of a unified diff: `- NAD:BY` / `+ NAD:BY` / `~ QTY:21`.
     */
    public function __toString(): string
    {
        $marker = match ($this->kind) {
            self::ADDED => '+',
            self::REMOVED => '-',
            default => '~',
        };

        return sprintf('%s message %d  %s:%s', $marker, $this->messageIndex, $this->tag, $this->subId);
    }

    public static function added(int $messageIndex, SegmentInterface $segment): self
    {
        return new self(self::ADDED, $messageIndex, $segment->tag(), $segment->subId(), null, $segment);
    }

    public static function removed(int $messageIndex, SegmentInterface $segment): self
    {
        return new self(self::REMOVED, $messageIndex, $segment->tag(), $segment->subId(), $segment, null);
    }

    public static function changed(int $messageIndex, SegmentInterface $before, SegmentInterface $after): self
    {
        return new self(self::CHANGED, $messageIndex, $after->tag(), $after->subId(), $before, $after);
    }

    public function kind(): string
    {
        return $this->kind;
    }

    /**
     * Which message of the interchange this difference sits in, zero-based.
     */
    public function messageIndex(): int
    {
        return $this->messageIndex;
    }

    public function tag(): string
    {
        return $this->tag;
    }

    public function subId(): string
    {
        return $this->subId;
    }

    public function before(): ?SegmentInterface
    {
        return $this->before;
    }

    public function after(): ?SegmentInterface
    {
        return $this->after;
    }

    /**
     * @return array{kind: string, message: int, tag: string, subId: string, before: array|null, after: array|null}
     */
    public function toArray(): array
    {
        return [
            'kind' => $this->kind,
            'message' => $this->messageIndex,
            'tag' => $this->tag,
            'subId' => $this->subId,
            'before' => $this->before === null ? null : SegmentArray::fromSegment($this->before)['rawValues'],
            'after' => $this->after === null ? null : SegmentArray::fromSegment($this->after)['rawValues'],
        ];
    }
}
