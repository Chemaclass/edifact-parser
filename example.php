<?php

declare(strict_types=1);

use EdifactParser\EdifactParser;
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

require __DIR__ . '/vendor/autoload.php';

$fileContent = <<<EDI
UNA:+.? '
UNB+UNOC:3+9457386:30+73130012:30+19101:118+8+MPM 2.19+1424'

UNH+1+IFTMIN:S:93A:UN:PN001'
BGM+340+56677786689+9'
DTM+10:20191011:102'
TSR+19+A4'
CNT+7:0.51:KGM'
CNT+11:1:PCE'
RFF+CU:ValidationSet1'
TDT+20'
NAD+CZ+0410106314:160:Z12++Company Centre+c/o Carrier AB+City1++12345+DE'
NAD+CN+++Person Name+Street Nr 2+City2++12345+DE'
CTA+IC+:Person Name'
COM+?+46980100:AL'
COM+person.name@test.com:EM'
GID+1+1'
MEA+WT+G+KGM:0.62'
MEA+VOL++MTQ:0'
PCI+18+56677786689'
UNT+18+1'

UNH+2+IFTMIN:S:93A:UN:PN001'
BGM+340+05055700896+9'
DTM+10:20191011:102'
TSR+19+A4'
CNT+7:0.62:KGM'
CNT+11:1:PCE'
RFF+CU:ValidationSet2'
TDT+20'
NAD+CZ+0410106314:160:Z12++Company Returns+c/o Carrier AB+City1++12345+DE'
NAD+CN+++Person Name+Street Nr 2+City2++12345+DE'
CTA+IC+:Person Name'
COM+?+46980100:AL'
COM+person.name@test.com:EM'
GID+1+1'
MEA+WT+G+KGM:0.62'
MEA+VOL++MTQ:0'
PCI+18+05055700896'
UNT+19+2'

UNZ+2+8'
EDI;

$messages = EdifactParser::create()->parse($fileContent);

foreach ($messages as $i => $message) {
    print "Message number: {$i}\n";
    printMessage($message);
    print PHP_EOL;
}

function printMessage(TransactionMessage $message): void
{
    printSegment($message->segmentByName(UNHMessageHeader::class));
    printSegment($message->segmentByName(BGMBeginningOfMessage::class));
    printSegment($message->segmentByName(DTMDateTimePeriod::class));
    printSegment($message->segmentByName(CNTControl::class));
    printSegment($message->segmentByName(NADNameAddress::class));
    printSegment($message->segmentByName(MEADimensions::class));
    printSegment($message->segmentByName(PCIPackageId::class));
    printSegment($message->segmentByName(UNTMessageFooter::class));
}

/** @var SegmentInterface[] $segments */
function printSegment(array $segments): void
{
    $first = $segments[array_key_first($segments)];
    print sprintf("> %s:\n", $first->tag());

    foreach ($segments as $segment) {
        print sprintf(
            "    %s |> %s \n",
            str_pad($segment->subSegmentKey(), 3),
            json_encode($segment->rawValues())
        );
    }
}
