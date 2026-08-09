<?php

declare(strict_types=1);

namespace EdifactParser\Directory;

/**
 * One segment slot in a message structure: which tag, whether it is mandatory there, and
 * how often it may repeat.
 */
final class SegmentPosition
{
    public function __construct(
        private string $tag,
        private bool $required,
        private int $maxRepeat,
    ) {
    }

    public function tag(): string
    {
        return $this->tag;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function maxRepeat(): int
    {
        return $this->maxRepeat;
    }
}
