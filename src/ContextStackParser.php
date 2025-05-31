<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

use function array_key_exists;

final class ContextStackParser
{
    /** @var array<string, bool> */
    private const CONTEXT_TAGS = ['NAD' => true, 'LIN' => true, 'DOC' => true];

    /** @var array<string, bool> */
    private const CHILD_TAGS = [
        'COM' => true,
        'CTA' => true,
        'PIA' => true,
        'IMD' => true,
        'MEA' => true,
        'QTY' => true,
        'PRI' => true,
        'TAX' => true,
        'DTM' => true,
        'MOA' => true,
    ];

    /**
     * @return list<ContextSegment>
     */
    public function parse(SegmentInterface ...$segments): array
    {
        $result = [];
        $stack = [];

        foreach ($segments as $segment) {
            $tag = $segment->tag();

            if (array_key_exists($tag, self::CONTEXT_TAGS)) {
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

            if (array_key_exists($tag, self::CHILD_TAGS) && $stack !== []) {
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
