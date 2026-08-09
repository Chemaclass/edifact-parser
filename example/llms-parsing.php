<?php

declare(strict_types=1);

/**
 * Executable version of docs/llms/parsing.md. Run by CI so the documentation cannot rot.
 */

use EdifactParser\EdifactParser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\StreamingParser;
use EdifactParser\Tokenizer\SabasTokenizer;

require __DIR__ . '/../vendor/autoload.php';

$path = __DIR__ . '/edifact-sample.edi';
$edi = (string) file_get_contents($path);

$result = EdifactParser::createWithDefaultSegments()->parse($edi);
assert(EdifactParser::createWithDefaultSegments()->parseFile($path)->count() === $result->count());

// --- ParserResult -----------------------------------------------------------
assert($result->transactionMessages() !== []);
assert($result->firstMessage() !== null);
assert($result->messagesOfType('IFTMIN') !== []);
assert($result->messagesOfType('ORDERS') === []);
assert($result->functionalGroups() === []);
assert($result->globalSegments()->has('UNB'));
assert(count($result) === 2);

foreach ($result as $message) {
    assert($message->messageType() === 'IFTMIN');
}

// --- TransactionMessage -----------------------------------------------------
$message = $result->firstMessage();
assert($message !== null);
assert($message->segments() !== []);
assert($message->countByTag()['NAD'] === 2);
assert($message->has('NAD') === true);
assert($message->has('ZZZ') === false);
assert(count($message) === 18);

foreach ($message as $segment) {
    assert($segment->tag() !== '');
}

$asArray = $message->toArray();
assert($asArray['type'] === 'IFTMIN');
assert(json_decode($message->toJson(), true) === $asArray);

// --- Tokenizers -------------------------------------------------------------
$withSabas = new EdifactParser(SegmentFactory::withDefaultSegments(), tokenizer: new SabasTokenizer());
assert($withSabas->parse($edi)->count() === 2);

// --- Streaming --------------------------------------------------------------
$streamed = 0;
foreach (StreamingParser::createWithDefaultSegments()->parseFile($path) as $streamedMessage) {
    assert($streamedMessage->messageType() === 'IFTMIN');
    ++$streamed;
}
assert($streamed === 2);

// --- Errors -----------------------------------------------------------------
try {
    EdifactParser::createWithDefaultSegments()->parse("UNH+1+ORDERS'NAD+BY");
    assert(false, 'expected InvalidFile');
} catch (InvalidFile $e) {
    assert($e->getErrors() !== []);
    assert($e->getDiagnostics()[0]->code() === 'segment.unterminated');
}

echo "docs/llms/parsing.md OK\n";
