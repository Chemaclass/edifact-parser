<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

use function in_array;

final class ContextStackParser
{
    /** @var list<string> */
    private const CONTEXT_TAGS = ['NAD', 'LIN', 'DOC'];

    /** @var list<string> */
    private const CHILD_TAGS = ['COM', 'CTA', 'PIA', 'IMD', 'MEA', 'QTY', 'PRI', 'TAX', 'DTM', 'MOA'];

    /**
     * @return list<ContextSegment>
     */
    public function parse(SegmentInterface ...$segments): array
    {
        $result = [];
        $stack = [];

        foreach ($segments as $segment) {
            $tag = $segment->tag();

            if (in_array($tag, self::CONTEXT_TAGS, true)) {
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

            if (in_array($tag, self::CHILD_TAGS, true) && $stack !== []) {
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
