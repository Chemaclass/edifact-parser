<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

final class ContextStackParser
{
    private GroupingRules $rules;

    public function __construct(?GroupingRules $rules = null)
    {
        $this->rules = $rules ?? GroupingRules::default();
    }

    /**
     * @return list<ContextSegment>
     */
    public function parse(SegmentInterface ...$segments): array
    {
        $result = [];
        $stack = [];

        foreach ($segments as $segment) {
            $tag = $segment->tag();

            if ($this->rules->isContextTag($tag)) {
                // Contexts are single-level: a new context tag closes the previous one
                // and starts a fresh top-level context that later child tags attach to.
                $context = new ContextSegment($segment);
                $result[] = $context;
                $stack = [$context];
                continue;
            }

            if ($this->rules->isChildTag($tag) && $stack !== []) {
                $stack[array_key_last($stack)]->addChild($segment);
                continue;
            }

            if ($tag === 'UNT') {
                $stack = [];
            }
        }

        return $result;
    }
}
