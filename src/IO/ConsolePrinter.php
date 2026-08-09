<?php

declare(strict_types=1);

namespace EdifactParser\IO;

use EdifactParser\Segments\SegmentInterface;
use EdifactParser\TransactionMessage;

use function json_encode;
use function sprintf;
use function str_pad;

final class ConsolePrinter implements PrinterInterface
{
    private function __construct(
        /** @var list<string> */
        private array $segmentNames
    ) {
    }

    /**
     * @param list<string> $segmentNames
     */
    public static function createWithHeaders(array $segmentNames): self
    {
        return new self($segmentNames);
    }

    public function printMessage(TransactionMessage $message): void
    {
        foreach ($this->segmentNames as $segmentName) {
            $segments = $message->segmentsByTag($segmentName);
            if ($segments === []) {
                continue;
            }
            $this->printSegmentWithContext($message, $segments);
        }
    }

    /**
     * Prints segments and inline context if present.
     *
     * @param  array<array-key, SegmentInterface>  $segments
     */
    private function printSegmentWithContext(TransactionMessage $message, array $segments): void
    {
        $headerPrinted = false;

        foreach ($segments as $segment) {
            if (!$headerPrinted) {
                echo sprintf("%s:\n", $segment->tag());
                $headerPrinted = true;
            }

            $this->printSingleSegmentWithContext($message, $segment);
        }
    }

    /**
     * Handles printing of a segment inline with the children grouped under it. Keyed
     * lookups hand back the typed segment, so the children come from the message.
     */
    private function printSingleSegmentWithContext(
        TransactionMessage $message,
        SegmentInterface $segment,
        string $indent = '  ',
    ): void {
        $subId = $segment->subId();
        $values = json_encode($segment->rawValues(), JSON_THROW_ON_ERROR);

        echo sprintf("%s%s |> %s\n", $indent, str_pad($subId, 3), $values);

        foreach ($message->childrenOf($segment) as $child) {
            $this->printSingleSegmentWithContext($message, $child, $indent . '  ');
        }
    }
}
