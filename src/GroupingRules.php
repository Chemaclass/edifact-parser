<?php

declare(strict_types=1);

namespace EdifactParser;

use function in_array;

/**
 * Configurable rules that drive how segments are grouped into context hierarchies
 * and line-item sections. Defaults match the standard behaviour; pass a customized
 * instance to {@see EdifactParser} to change context parents/children or the tags
 * that close a line-item section.
 */
final class GroupingRules
{
    /**
     * @param list<string> $contextTags Tags that open a context (parent) segment
     * @param list<string> $childTags Tags attached as children to the open context
     * @param list<string> $breakLineItemTags Tags that end the line-item section
     */
    public function __construct(
        private array $contextTags = ['NAD', 'LIN', 'DOC'],
        private array $childTags = ['COM', 'CTA', 'PIA', 'IMD', 'MEA', 'QTY', 'PRI', 'TAX', 'DTM', 'MOA'],
        private array $breakLineItemTags = ['UNS', 'CNT', 'UNT'],
    ) {
    }

    public static function default(): self
    {
        return new self();
    }

    /**
     * @param list<string> $contextTags
     */
    public function withContextTags(array $contextTags): self
    {
        $clone = clone $this;
        $clone->contextTags = $contextTags;

        return $clone;
    }

    /**
     * @param list<string> $childTags
     */
    public function withChildTags(array $childTags): self
    {
        $clone = clone $this;
        $clone->childTags = $childTags;

        return $clone;
    }

    /**
     * @param list<string> $breakLineItemTags
     */
    public function withBreakLineItemTags(array $breakLineItemTags): self
    {
        $clone = clone $this;
        $clone->breakLineItemTags = $breakLineItemTags;

        return $clone;
    }

    public function isContextTag(string $tag): bool
    {
        return in_array($tag, $this->contextTags, true);
    }

    public function isChildTag(string $tag): bool
    {
        return in_array($tag, $this->childTags, true);
    }

    public function isBreakLineItemTag(string $tag): bool
    {
        return in_array($tag, $this->breakLineItemTags, true);
    }
}
