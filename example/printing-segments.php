<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use EdifactParser\EdifactParser;
use EdifactParser\IO\ConsolePrinter;
use EdifactParser\Segments\BGMBeginningOfMessage;
use EdifactParser\Segments\CNTControl;
use EdifactParser\Segments\DTMDateTimePeriod;
use EdifactParser\Segments\MEADimensions;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\PCIPackageId;
use EdifactParser\Segments\UNHMessageHeader;
use EdifactParser\Segments\UNTMessageFooter;

$fileContent = file_get_contents(__DIR__ . '/edifact-sample.edi');
$messages = EdifactParser::createWithDefaultSegments()->parse($fileContent);

$printer = ConsolePrinter::createWithHeaders([
    UNHMessageHeader::class,
    BGMBeginningOfMessage::class,
    DTMDateTimePeriod::class,
    CNTControl::class,
    NADNameAddress::class,
    MEADimensions::class,
    PCIPackageId::class,
    UNTMessageFooter::class,
]);

foreach ($messages as $i => $message) {
    print "Message number: {$i}\n";
    $printer->printMessage($message);
    print PHP_EOL;
}
