<?php

declare(strict_types=1);

namespace EdifactParser\Validation;

/**
 * A conformance rule set for one message type: which segment tags are required
 * and, optionally, how many times each tag may occur.
 *
 * Rule sets are plain data — build your own per message type, or extend the
 * fluent helpers below.
 */
final class MessageRuleSet
{
    /**
     * @param list<string> $requiredTags
     * @param array<string, array{min: int, max: int|null}> $cardinality
     * @param list<string> $sequence Expected relative order of the listed tags
     */
    public function __construct(
        private string $messageType,
        private array $requiredTags = [],
        private array $cardinality = [],
        private array $sequence = [],
    ) {
    }

    public static function forType(string $messageType): self
    {
        return new self($messageType);
    }

    public function require(string ...$tags): self
    {
        $clone = clone $this;
        $clone->requiredTags = [...$this->requiredTags, ...array_values($tags)];

        return $clone;
    }

    public function occurs(string $tag, int $min, ?int $max = null): self
    {
        $clone = clone $this;
        $clone->cardinality[$tag] = ['min' => $min, 'max' => $max];

        return $clone;
    }

    /**
     * Declare the expected relative order of the given tags. Other tags are ignored.
     */
    public function inSequence(string ...$tags): self
    {
        $clone = clone $this;
        $clone->sequence = array_values($tags);

        return $clone;
    }

    public function messageType(): string
    {
        return $this->messageType;
    }

    /**
     * @return list<string>
     */
    public function requiredTags(): array
    {
        return $this->requiredTags;
    }

    /**
     * @return array<string, array{min: int, max: int|null}>
     */
    public function cardinality(): array
    {
        return $this->cardinality;
    }

    /**
     * @return list<string>
     */
    public function sequence(): array
    {
        return $this->sequence;
    }
}
