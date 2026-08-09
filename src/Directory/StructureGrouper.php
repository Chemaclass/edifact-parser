<?php

declare(strict_types=1);

namespace EdifactParser\Directory;

use EdifactParser\Segments\SegmentInterface;

use function count;

/**
 * Groups a message's segments into the nested segment groups its directory defines.
 *
 * This is what {@see \EdifactParser\GroupingRules} approximates: that one applies a single
 * flat parent/child tag list to every message type, while the standard defines a distinct,
 * arbitrarily nested structure per message and directory — ORDERS D96A alone has 54 groups.
 *
 * The matcher walks segments and structure positions together, greedily: a position
 * consumes consecutive segments with its tag up to its repeat limit, and a group opens
 * whenever the next segment carries its trigger tag. Where the same tag could belong to
 * more than one level, document order decides — as it does in the standard.
 */
final class StructureGrouper
{
    /**
     * @param iterable<SegmentInterface> $segments
     *
     * @return list<GroupInstance|SegmentInterface> ungrouped segments and group occurrences, in order
     */
    public function group(iterable $segments, MessageStructure $structure): array
    {
        $ordered = [];
        foreach ($segments as $segment) {
            $ordered[] = $segment;
        }

        $offset = 0;
        $nodes = $this->consume($structure->parts(), $ordered, $offset);

        // Anything the structure did not account for still belongs to the message: a
        // parser that silently drops segments is worse than one that groups imperfectly.
        for ($index = $offset, $total = count($ordered); $index < $total; ++$index) {
            $nodes[] = $ordered[$index];
        }

        return $nodes;
    }

    /**
     * @param list<SegmentPosition|SegmentGroup> $parts
     * @param list<SegmentInterface> $segments
     *
     * @return list<GroupInstance|SegmentInterface>
     */
    private function consume(array $parts, array $segments, int &$offset): array
    {
        $nodes = [];
        $total = count($segments);

        foreach ($parts as $part) {
            if ($part instanceof SegmentPosition) {
                $taken = 0;

                while ($offset < $total
                    && $taken < $part->maxRepeat()
                    && $segments[$offset]->tag() === $part->tag()
                ) {
                    $nodes[] = $segments[$offset];
                    ++$offset;
                    ++$taken;
                }

                continue;
            }

            $trigger = $part->triggerTag();

            if ($trigger === null) {
                continue;
            }

            $occurrence = 0;

            while ($offset < $total
                && $occurrence < $part->maxRepeat()
                && $segments[$offset]->tag() === $trigger
            ) {
                $nodes[] = $this->consumeGroup($part, $segments, $offset, $occurrence);
                ++$occurrence;
            }
        }

        return $nodes;
    }

    /**
     * @param list<SegmentInterface> $segments
     */
    private function consumeGroup(
        SegmentGroup $group,
        array $segments,
        int &$offset,
        int $occurrence,
    ): GroupInstance {
        $inner = $this->consume($group->parts(), $segments, $offset);

        $direct = [];
        $children = [];

        foreach ($inner as $node) {
            if ($node instanceof GroupInstance) {
                $children[] = $node;
            } else {
                $direct[] = $node;
            }
        }

        return new GroupInstance($group->id(), $occurrence, $direct, $children);
    }
}
