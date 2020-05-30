<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;

final class TransactionMessage
{
    /**
     * @psalm-pure
     * @return array<array<string, array<string,SegmentInterface>>>
     */
    public static function fromSegmentedValues(SegmentInterface...$segments): array
    {
        $messages = [];
        $segmentsGroup = [];

        foreach ($segments as $segment) {
            if ($segment instanceof UNHMessageHeader) {
                if ($segmentsGroup) {
                    $messages[] = static::withSegments(...$segmentsGroup);
                }
                $segmentsGroup = [];
            }
            $segmentsGroup[] = $segment;
        }

        $messages[] = static::withSegments(...$segmentsGroup);

        return $messages;
    }

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
