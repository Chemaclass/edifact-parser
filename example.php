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
use EDI\Parser;

require __DIR__ . '/bootstrap.php';

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
UNT+18+2'

UNZ+2+8'
EDI;

$transactionResult = EdifactParser::parse(new Parser($fileContent));
$firstMessage = $transactionResult->messages()[0];
$segments = $firstMessage->segments();

printSegment($segments[UNHMessageHeader::NAME]['1']);
printSegment($segments[BGMBeginningOfMessage::NAME]['340']);
printSegment($segments[DTMDateTimePeriod::NAME]['10']);
printSegment($segments[CNTControl::NAME]['7']);
printSegment($segments[CNTControl::NAME]['11']);
printSegment($segments[NADNameAddress::NAME]['CZ']);
printSegment($segments[MEADimensions::NAME]['WT']);
printSegment($segments[MEADimensions::NAME]['VOL']);
printSegment($segments[PCIPackageId::NAME]['18']);
printSegment($segments[UNTMessageFooter::NAME]['18']);

function printSegment(SegmentInterface $segment): void
{
    echo sprintf('%s - %s %s',$segment->name(),$segment->subSegmentKey(),PHP_EOL);
}
