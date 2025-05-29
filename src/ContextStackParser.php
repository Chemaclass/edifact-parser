<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

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
        $current = null;

        foreach ($segments as $segment) {
            $tag = $segment->tag();

            if (in_array($tag, self::CONTEXT_TAGS, true)) {
                $context = new ContextSegment($segment);
                $result[] = $context;
                $current = $context;
                continue;
            }

            if (in_array($tag, self::CHILD_TAGS, true) && $current !== null) {
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
