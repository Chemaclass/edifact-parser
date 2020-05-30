<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;

final class TransactionMessage
{
    /**
     * @psalm-pure
     * @return array<string, array<string,SegmentInterface>> First key: segment name, second key: subSegment key.
     */
    public static function withSegments(SegmentInterface...$segments): array
    {
        $return = [];

        foreach ($segments as $segment) {
            $name = $segment->name();

            if (!isset($return[$name])) {
                $return[$name] = [];
            }

            $return[$name][$segment->subSegmentKey()] = $segment;
        }

        return $return;
    }
}
