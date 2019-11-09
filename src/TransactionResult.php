<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;

/**
 * A transactionResult is a list of transactionMessages.
 */
final class TransactionResult
{
    /** @var TransactionMessage[] */
    private $messages;

    private function __construct(SegmentedValues $values)
    {
        /** @var TransactionMessage[] $messages */
        $messages = [];
        /** @var ?TransactionMessage $message */
        $message = null;

        /** @var SegmentInterface $segment */
        foreach ($values->list() as $segment) {
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

        $this->messages = $messages;
    }

    public static function fromSegmentedValues(SegmentedValues $values): self
    {
        return new self($values);
    }

    /** @return TransactionMessage[] */
    public function messages(): array
    {
        return $this->messages;
    }
}
