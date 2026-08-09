<?php

declare(strict_types=1);

namespace EdifactParser;

use function array_fill_keys;

/**
 * Configurable rules that drive how segments are grouped into context hierarchies
 * and line-item sections. Defaults match the standard behaviour; pass a customized
 * instance to {@see EdifactParser} to change context parents/children or the tags
 * that close a line-item section.
 *
 * Lookups are backed by hash maps built once per instance, so `isContextTag()` &
 * friends stay O(1) no matter how many tags are configured — they run once per
 * parsed segment.
 */
final class GroupingRules
{
    public const DEFAULT_CONTEXT_TAGS = ['NAD', 'LIN', 'DOC'];

    public const DEFAULT_CHILD_TAGS = ['COM', 'CTA', 'PIA', 'IMD', 'MEA', 'QTY', 'PRI', 'TAX', 'DTM', 'MOA'];

    public const DEFAULT_BREAK_LINE_ITEM_TAGS = ['UNS', 'CNT', 'UNT'];

    /** @var array<array-key, true> */
    private array $contextTagIndex;

    /** @var array<array-key, true> */
    private array $childTagIndex;

    /** @var array<array-key, true> */
    private array $breakLineItemTagIndex;

    /**
     * @param list<string> $contextTags Tags that open a context (parent) segment
     * @param list<string> $childTags Tags attached as children to the open context
     * @param list<string> $breakLineItemTags Tags that end the line-item section
     */
    public function __construct(
        private array $contextTags = self::DEFAULT_CONTEXT_TAGS,
        private array $childTags = self::DEFAULT_CHILD_TAGS,
        private array $breakLineItemTags = self::DEFAULT_BREAK_LINE_ITEM_TAGS,
    ) {
        $this->contextTagIndex = array_fill_keys($contextTags, true);
        $this->childTagIndex = array_fill_keys($childTags, true);
        $this->breakLineItemTagIndex = array_fill_keys($breakLineItemTags, true);
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
        return new self($contextTags, $this->childTags, $this->breakLineItemTags);
    }

    /**
     * @param list<string> $childTags
     */
    public function withChildTags(array $childTags): self
    {
        return new self($this->contextTags, $childTags, $this->breakLineItemTags);
    }

    /**
     * @param list<string> $breakLineItemTags
     */
    public function withBreakLineItemTags(array $breakLineItemTags): self
    {
        return new self($this->contextTags, $this->childTags, $breakLineItemTags);
    }

    /**
     * @return list<string>
     */
    public function contextTags(): array
    {
        return $this->contextTags;
    }

    /**
     * @return list<string>
     */
    public function childTags(): array
    {
        return $this->childTags;
    }

    /**
     * @return list<string>
     */
    public function breakLineItemTags(): array
    {
        return $this->breakLineItemTags;
    }

    public function isContextTag(string $tag): bool
    {
        return isset($this->contextTagIndex[$tag]);
    }

    public function isChildTag(string $tag): bool
    {
        return isset($this->childTagIndex[$tag]);
    }

    public function isBreakLineItemTag(string $tag): bool
    {
        return isset($this->breakLineItemTagIndex[$tag]);
    }
}
