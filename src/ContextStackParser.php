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
        $current = null;

        foreach ($segments as $segment) {
            $tag = $segment->tag();

            if ($this->rules->isContextTag($tag)) {
                // Contexts are single-level: a new context tag closes the previous one
                // and starts a fresh top-level context that later child tags attach to.
                $current = new ContextSegment($segment);
                $result[] = $current;
                continue;
            }

            if ($current !== null && $this->rules->isChildTag($tag)) {
                $current->addChild($segment);
                continue;
            }

            if ($tag === 'UNT') {
                $current = null;
            }
        }

        return $result;
    }
}
