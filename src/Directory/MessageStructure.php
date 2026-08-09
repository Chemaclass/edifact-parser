<?php

declare(strict_types=1);

namespace EdifactParser\Directory;

use function count;

/**
 * The structure a UN/EDIFACT directory defines for one message type: an ordered list of
 * segment positions and nested segment groups.
 *
 * This is the real thing the library's {@see \EdifactParser\GroupingRules} only
 * approximates — ORDERS D96A has dozens of groups, several levels deep, each with its own
 * occurrence limits.
 */
final class MessageStructure
{
    /**
     * @param list<SegmentPosition|SegmentGroup> $parts
     */
    public function __construct(
        private string $messageType,
        private array $parts,
    ) {
    }

    public function messageType(): string
    {
        return $this->messageType;
    }

    /**
     * @return list<SegmentPosition|SegmentGroup>
     */
    public function parts(): array
    {
        return $this->parts;
    }

    /**
     * Every group in the structure, nested ones included, keyed by id.
     *
     * @return array<string, SegmentGroup>
     */
    public function groups(): array
    {
        return self::collectGroups($this->parts);
    }

    public function group(string $id): ?SegmentGroup
    {
        return $this->groups()[$id] ?? null;
    }

    public function groupCount(): int
    {
        return count($this->groups());
    }

    /**
     * @param list<SegmentPosition|SegmentGroup> $parts
     *
     * @return array<string, SegmentGroup>
     */
    private static function collectGroups(array $parts): array
    {
        $groups = [];

        foreach ($parts as $part) {
            if (!$part instanceof SegmentGroup) {
                continue;
            }

            $groups[$part->id()] = $part;
            // array_merge, not spread: unpacking string-keyed arrays needs PHP 8.1 and the
            // package supports 8.0.
            $groups = array_merge($groups, self::collectGroups($part->parts()));
        }

        return $groups;
    }
}
