<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\ReadModel\MessageSection;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;

final class TransactionMessage
{
    /**
     * @psalm-pure
     * @return MessageSection[]
     */
    public static function fromSegmentedValues(SegmentInterface...$segments): array
    {
        $messages = [];
        $segmentsGroup = [];

        foreach ($segments as $segment) {
            if ($segment instanceof UNHMessageHeader) {
                if ($segmentsGroup) {
                    $messages[] = MessageSection::fromSegments(...$segmentsGroup);
                }
                $segmentsGroup = [];
            }
            $segmentsGroup[] = $segment;
        }

        $messages[] = MessageSection::fromSegments(...$segmentsGroup);

        return $messages;
    }
}
