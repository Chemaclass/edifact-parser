<?php

declare(strict_types=1);

namespace EdifactParser\IO;

use EdifactParser\Segments\SegmentInterface;
use EdifactParser\TransactionMessage;

final class ConsolePrinter implements PrinterInterface
{
    /** @var string[] */
    private array $segmentNames;

    public static function createWithHeaders(array $segmentNames): self
    {
        return new self($segmentNames);
    }

    private function __construct(array $segmentNames)
    {
        $this->segmentNames = $segmentNames;
    }

    public function printMessage(TransactionMessage $message): void
    {
        foreach ($this->segmentNames as $segmentName) {
            $this->printSegment($message->segmentsByTag($segmentName));
        }
    }

    /** @var SegmentInterface[] */
    private function printSegment(array $segments): void
    {
        $first = $segments[array_key_first($segments)];
        print sprintf("> %s:\n", $first->tag());

        foreach ($segments as $segment) {
            print sprintf(
                "    %s |> %s \n",
                str_pad($segment->subId(), 3),
                json_encode($segment->rawValues())
            );
        }
    }
}
