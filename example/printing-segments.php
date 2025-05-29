<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use EdifactParser\EdifactParser;
use EdifactParser\IO\ConsolePrinter;

$fileContent = file_get_contents(__DIR__ . '/edifact-sample.edi');
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

    print "Context segments:\n";
    foreach ($message->contextSegments() as $context) {
        $parent = $context->segment();
        printf("> %s %s\n", $parent->tag(), $parent->subId());
        foreach ($context->children() as $child) {
            printf(
                "    - %s |> %s\n",
                $child->tag(),
                json_encode($child->rawValues(), JSON_THROW_ON_ERROR)
            );
        }
    }
}
