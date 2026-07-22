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
                $context = new ContextSegment($segment);

                // Pop previous context since this is a new one at the same level
                if ($stack !== []) {
                    array_pop($stack);
                }

                if ($stack === []) { // @phpstan-ignore-line
                    $result[] = $context;
                } else {
                    $stack[array_key_last($stack)]->addChild($context);
                }

                $stack[] = $context;
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
