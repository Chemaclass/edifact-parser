<?php

declare(strict_types=1);

namespace EdifactParser\IO;

use EdifactParser\Segments\NullSegment;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\TransactionMessage;

final class ConsolePrinter implements PrinterInterface
{
    /** @var list<string> */
    private array $segmentNames;

    private function __construct(array $segmentNames)
    {
        $this->segmentNames = $segmentNames;
    }

    public static function createWithHeaders(array $segmentNames): self
    {
        return new self($segmentNames);
    }

    public function printMessage(TransactionMessage $message): void
    {
        foreach ($this->segmentNames as $segmentName) {
            $this->printSegment($message->segmentsByTag($segmentName));
        }
    }

    /**
     * @phpstan-impure
     *
     * @param array<string,SegmentInterface> $segments
     */
    private function printSegment(array $segments): void
    {
        $key = array_key_first($segments);
        $first = $segments[$key] ?? new NullSegment();

        echo sprintf("> %s:\n", $first->tag());

        foreach ($segments as $segment) {
            echo sprintf(
                "    %s |> %s \n",
                str_pad($segment->subId(), 3),
                json_encode($segment->rawValues(), JSON_THROW_ON_ERROR)
            );
        }
    }
}
