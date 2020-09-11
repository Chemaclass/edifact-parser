<?php

declare(strict_types=1);

namespace EdifactParser\IO;

use EdifactParser\Segments\BGMBeginningOfMessage;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\DTMDateTimePeriod;
use EdifactParser\Segments\MEADimensions;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\PCIPackageId;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;
use EdifactParser\TransactionMessage;

final class ConsolePrinter implements PrinterInterface
{
    public function printMessage(TransactionMessage $message): void
    {
        $this->printSegment($message->segmentByName(UNHMessageHeader::class));
        $this->printSegment($message->segmentByName(BGMBeginningOfMessage::class));
        $this->printSegment($message->segmentByName(DTMDateTimePeriod::class));
        $this->printSegment($message->segmentByName(CNTControl::class));
        $this->printSegment($message->segmentByName(NADNameAddress::class));
        $this->printSegment($message->segmentByName(MEADimensions::class));
        $this->printSegment($message->segmentByName(PCIPackageId::class));
        $this->printSegment($message->segmentByName(UNTMessageFooter::class));
    }

    /** @var SegmentInterface[] $segments */
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
