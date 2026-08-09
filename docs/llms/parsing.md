# Parsing

Runnable version: [`example/llms-parsing.php`](../../example/llms-parsing.php)

## Parse a string or a file

```php
use EdifactParser\EdifactParser;

$result = EdifactParser::createWithDefaultSegments()->parse($ediString);
$result = EdifactParser::createWithDefaultSegments()->parseFile('/path/to/order.edi');
```

Both return a `ParserResult`. Invalid input throws `EdifactParser\Exception\InvalidFile`.

## ParserResult

```php
$result->transactionMessages();     // list<TransactionMessage> — the UNH…UNT blocks
$result->firstMessage();            // ?TransactionMessage
$result->messagesOfType('INVOIC');  // list<TransactionMessage>
$result->functionalGroups();        // list<FunctionalGroup> — UNG…UNE, empty when unused
$result->globalSegments();          // TransactionMessage — file-level UNA/UNB/UNZ

count($result);                     // number of messages
foreach ($result as $message) { }   // iterate messages
```

## TransactionMessage

```php
$message->messageType();   // 'ORDERS' | 'INVOIC' | … | null when there is no UNH
$message->segments();      // list<SegmentInterface>, document order, duplicates kept
$message->countByTag();    // ['UNH' => 1, 'NAD' => 2, …]
$message->has('QTY');      // bool
count($message);           // total segments
foreach ($message as $segment) { }

$message->toArray();       // ['type' => 'ORDERS', 'segments' => [...]]
$message->toJson();
```

## Tokenizers

`NativeTokenizer` is the default: regex-free, ~2.2× faster at tokenizing, and it preserves
non-ASCII bytes.

```php
use EdifactParser\Tokenizer\SabasTokenizer;
use EdifactParser\Segments\SegmentFactory;

// Only if you need bug-for-bug compatibility with 6.x. It strips \x80-\xFF,
// so 'Müller' becomes 'Mller'.
new EdifactParser(SegmentFactory::withDefaultSegments(), tokenizer: new SabasTokenizer());
```

## Streaming

One message in memory at a time, for arbitrarily large files. A leading `UNA` is honoured.

```php
use EdifactParser\StreamingParser;

foreach (StreamingParser::createWithDefaultSegments()->parseFile($path) as $message) {
    // $message is a TransactionMessage
}
```

## Errors

```php
use EdifactParser\Exception\InvalidFile;

try {
    $parser->parseFile($path);
} catch (InvalidFile $e) {
    $e->getErrors();       // list of strings
    $e->getDiagnostics();  // list<Diagnostic> — code, severity, segmentIndex, tag
}
```

See [validation](validation.md) for the diagnostic vocabulary.
