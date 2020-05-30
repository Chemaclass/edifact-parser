<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;

/** @psalm-immutable */
final class TransactionResult
{
    /**
     * @psalm-pure
     * @psalm-return list<array<string, array<string,SegmentInterface>>>
     */
    public static function fromSegmentedValues(SegmentInterface...$segments): array
    {
        $messages = [];
        $segmentsGroup = [];

        foreach ($segments as $segment) {
            if ($segment instanceof UNHMessageHeader) {
                if ($segmentsGroup) {
                    $messages[] = TransactionMessage::withSegments(...$segmentsGroup);
                }
                $segmentsGroup = [];
            }
            $segmentsGroup[] = $segment;
        }

        $messages[] = TransactionMessage::withSegments(...$segmentsGroup);

        return $messages;
    }
}
