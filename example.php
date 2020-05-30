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
NAD+CZ+0410106314:160:Z12++Comany Returns Centre+c/o Carrier AB+City1++12345+DE'
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
NAD+CZ+0410106314:160:Z12++Comany Returns Centre+c/o Carrier AB+City1++12345+DE'
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

$transactionResult = EdifactParser::create()->parse($fileContent);

foreach ($transactionResult as $i => $message) {
    print "Message number: {$i}" . PHP_EOL;
    printMessage($message);
    print PHP_EOL;
}

function printMessage(array $segments): void
{
    if (!isset($segments[UNHMessageHeader::class])) {
        print "No `UNHMessageHeader::class` segment was found \n";

        return;
    }

    $unhSubSegmentKey = array_key_first($segments[UNHMessageHeader::class]);
    printSegment($segments[UNHMessageHeader::class][$unhSubSegmentKey]);

    printSegment($segments[BGMBeginningOfMessage::class]['340']);
    printSegment($segments[DTMDateTimePeriod::class]['10']);
    printSegment($segments[CNTControl::class]['7']);
    printSegment($segments[CNTControl::class]['11']);
    printSegment($segments[NADNameAddress::class]['CZ']);
    printSegment($segments[MEADimensions::class]['WT']);
    printSegment($segments[MEADimensions::class]['VOL']);
    printSegment($segments[PCIPackageId::class]['18']);

    $untSubSegmentKey = array_key_first($segments[UNTMessageFooter::class]);
    printSegment($segments[UNTMessageFooter::class][$untSubSegmentKey]);
}

function printSegment(SegmentInterface $segment): void
{
    print sprintf(
        "%s - %s <| %s \n",
        str_pad($segment->name(), 44),
        str_pad($segment->subSegmentKey(), 3),
        json_encode($segment->rawValues())
    );
}
