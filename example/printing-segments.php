<?php

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

use EdifactParser\EdifactParser;
use EdifactParser\IO\ConsolePrinter;

$filepath = $argv[1] ?? __DIR__.'/edifact-sample.edi';
$fileContent = file_get_contents($filepath);
$parserResult = EdifactParser::createWithDefaultSegments()->parse($fileContent);

$printer = ConsolePrinter::createWithHeaders([
    'UNB',
    'UNH',
    'BGM',
    'DTM',
    'CNT',
    'NAD',
    'MEA',
    'PCI',
    'UNT',
]);


print "##################\n";
print "# Global segments:\n";
print "##################\n";
$printer->printMessage($parserResult->globalSegments());
print PHP_EOL;

foreach ($parserResult->transactionMessages() as $i => $message) {
    print "###################\n";
    print "# Message number: {$i}\n";
    print "###################\n";
    $printer->printMessage($message);
    print PHP_EOL;
}
