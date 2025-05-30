<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use EdifactParser\ContextSegment;
use EdifactParser\EdifactParser;

$filepath = $argv[1] ?? __DIR__ . '/edifact-sample.edi';
$fileContent = file_get_contents($filepath);
$parserResult = EdifactParser::createWithDefaultSegments()->parse($fileContent);

foreach ($parserResult->transactionMessages() as $i => $message) {
    echo "Message {$i}" . PHP_EOL;
    foreach ($message->contextSegments() as $context) {
        printContext($context);
    }
    echo PHP_EOL;
}

function printContext(ContextSegment $context, int $indent = 0): void
{
    $pad = str_repeat('  ', $indent);
    $values = json_encode($context->rawValues(), JSON_THROW_ON_ERROR);
    echo sprintf('%s%s %s -> %s' . PHP_EOL, $pad, $context->tag(), $context->subId(), $values);

    foreach ($context->children() as $child) {
        if ($child instanceof ContextSegment) {
            printContext($child, $indent + 1);
            continue;
        }
        $childPad = str_repeat('  ', $indent + 1);
        $childValues = json_encode($child->rawValues(), JSON_THROW_ON_ERROR);
        echo sprintf('%s%s %s -> %s' . PHP_EOL, $childPad, $child->tag(), $child->subId(), $childValues);
    }
}
