<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;

///** @psalm-immutable */
final class TransactionResult
{
    /** @psalm-return list<TransactionMessage> */
    public static function fromSegmentedValues(SegmentInterface...$segments): array
    {
        /** @var TransactionMessage[] $messages */
        $messages = [];
        /** @var ?TransactionMessage $message */
        $message = null;

        foreach ($segments as $segment) {
            if ($segment instanceof UNHMessageHeader) {
                if ($message) {
                    $messages[] = $message;
                }
                $message = new TransactionMessage();
            }

            if ($message) {
                $message->addSegment($segment);
            }
        }

        if ($message) {
            $messages[] = $message;
        }

        return $messages;
    }
}
