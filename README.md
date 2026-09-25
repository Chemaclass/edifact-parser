# EDIFACT Parser

[![Type Coverage](https://shepherd.dev/github/Chemaclass/EdifactParser/coverage.svg)](https://shepherd.dev/github/chemaclass/EdifactParser)
[![CI](https://github.com/Chemaclass/EdifactParser/workflows/CI/badge.svg?branch=main)](https://github.com/Chemaclass/EdifactParser/actions)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)

A PHP toolkit to parse, write, validate, and stream UN/EDIFACT interchanges. It runs on PHP 8.0 and later.

New to EDIFACT? It is the standard format for business documents such as orders and invoices. [Start with the format guide](docu/README.md).

## Install

```bash
composer require chemaclass/edifact-parser
```

Requires `ext-json` and `ext-mbstring`.

## Quick start

Save this as `order.php` after installing the package, then run `php order.php`:

```php
<?php

declare(strict_types=1);

use EdifactParser\EdifactParser;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;

require __DIR__ . '/vendor/autoload.php';

$edi = <<<'EDI'
    UNB+UNOC:3+SENDER+RECEIVER+240101:1200+1'
    UNH+1+ORDERS:D:96A:UN'
    BGM+220+PO-123+9'
    NAD+BY+123456++ACME Corporation+Street 1+Berlin++10115+DE'
    LIN+1++SKU-1:BP'
    QTY+21:100:PCE'
    UNT+6+1'
    UNZ+1+1'
    EDI;

$message = EdifactParser::createWithDefaultSegments()->parse($edi)->firstMessage();
$buyer = $message?->segmentOfType(NADNameAddress::class, 'BY');
$quantity = $message?->lineItemById(1)?->segmentOfType(QTYQuantity::class, '21');

printf("%s: %s, %.0f items\n", $message?->messageType(), $buyer?->name(), $quantity?->quantityAsFloat());
// ORDERS: ACME Corporation, 100 items
```

For a file, call `parseFile('/path/to/order.edi')` instead of `parse($edi)`. `firstMessage()` and typed lookups return `null` when the requested data is absent. The [runnable Quick Start](example/readme-quick-start.php) checks the output.

## Guide

- [Parsing](#parsing): strings, files, tokenizers, and streaming
- [Reading data](#reading-data): typed accessors, queries, line items, and envelope metadata
- [Writing EDIFACT](#writing-edifact): segment builders and full interchanges
- [Validation](#validation): message rules and diagnostics
- [Command line](#command-line): inspect, validate, and compare files without PHP code
- [Extending](#extending): custom segments, introspection, and grouping rules
- [Debugging](#debugging) and [development](#development)
- [Upgrading from 6.x](UPGRADING.md)

## Why this library

- **Read any message type.** Unknown tags remain available as `UnknownSegment` objects with `rawValues()`. The default factory types 32 tags. [Directory segments](docs/llms/extending.md#directory-segments) bring the total to 134, and you can add your own [custom segments](#extending).
- **Write and validate.** Serialize segments or build `UNB…UNZ` interchanges with automatic control counts. Rule sets check required tags, counts, and order. Directory data adds [element validation](docs/llms/validation.md#directory-validation) and [nested `SG1…SGn` groups](docs/llms/reading.md#segment-groups-directory-driven).
- **Handle large interchanges.** Streaming keeps one message in memory at a time. The parser preserves duplicate segments and exposes typed metadata for the interchange, functional groups, and messages.
- **Find data quickly.** Typed accessors, qualifier constants, a fluent query API, and a statistics analyzer cover common extraction tasks. Results, messages, line items, and queries are countable and iterable. Messages and segments also render as arrays or JSON.
- **Use it from a terminal.** The CLI prints JSON on stdout, parse errors on stderr, and uses documented exit codes. Structured diagnostics have stable codes. No console framework is required.
- **Keep the input intact.** The default tokenizer preserves non-ASCII bytes and the library maps `UNOA` through `UNOY` to their encodings. The PSR-4 API is tested with PHPUnit, PHPStan, Psalm, Rector, and PHP-CS-Fixer.

For coding agents, [llms.txt](llms.txt) links to focused [API guides](docs/llms/). Each snippet there has a runnable counterpart in [`example/`](example).

## Parsing

`EdifactParser::parse()` / `parseFile()` return a `ParserResult`:

```php
$result = EdifactParser::createWithDefaultSegments()->parse($ediString);

$result->transactionMessages();  // list<TransactionMessage> - the UNH…UNT blocks
$result->functionalGroups();     // list<FunctionalGroup> - UNG…UNE groups, if any
$result->globalSegments();       // TransactionMessage - file-level UNB/UNZ

$result->firstMessage();             // ?TransactionMessage
$result->messagesOfType('INVOIC');   // list<TransactionMessage> - an interchange may mix types
count($result);                      // number of messages
foreach ($result as $message) { … }  // iterate the messages directly
```

A **message** starts at `UNH` and ends at `UNT`; an **interchange** wraps messages between
`UNB` and `UNZ`, optionally grouped by `UNG`/`UNE`. Invalid input throws
[`InvalidFile`](#error-handling).

### Choosing a tokenizer

Turning raw text into segments is pluggable. **`NativeTokenizer` is the default**. On a valid 2.6 MB benchmark corpus accepted by both tokenizers, it is about 1.8× faster at tokenizing and 1.3× faster for `parse()` overall. It preserves the bytes it reads:

```php
$parser = EdifactParser::createWithDefaultSegments();   // NativeTokenizer
```

`SabasTokenizer` delegates to `sabas/edifact`, which was the default up to 6.x. It is still
available, but be aware it **strips every byte in `\x80-\xFF`** - `NAD+BY+++Müller` comes
back as `Mller`:

```php
use EdifactParser\Tokenizer\SabasTokenizer;
use EdifactParser\Segments\SegmentFactory;

new EdifactParser(SegmentFactory::withDefaultSegments(), tokenizer: new SabasTokenizer());
```

Reach for it when you need bug-for-bug compatibility with 6.x, or want the restricted
`UNOB` repertoire enforced. Otherwise the default is both faster and lossless.

For ASCII input the two tokenize identically - verified segment-for-segment across the test
fixtures and a generated corpus.

---

### Streaming large files

Stream messages one at a time in **bounded memory** - ideal for large interchanges. A
leading `UNA` service-string advice (custom separators/release char) is honoured
automatically:

```php
use EdifactParser\StreamingParser;

foreach (StreamingParser::createWithDefaultSegments()->parseFile('/path/to/large.edi') as $message) {
    echo $message->messageType(), PHP_EOL;
}
```

---

## Command line

Composer installs the CLI at `vendor/bin/edifact`. Run it from your project root to inspect a file without writing PHP:

```bash
vendor/bin/edifact parse order.edi                 # envelope, messages, and groups as JSON
vendor/bin/edifact inspect order.edi               # type, counts by tag, line items
vendor/bin/edifact validate order.edi              # rule set picked from the message type
vendor/bin/edifact validate order.edi --rules=ORDERS
vendor/bin/edifact segments                        # every tag the parser knows
vendor/bin/edifact segments --tag=NAD              # its accessors and return types
vendor/bin/edifact diff before.edi after.edi       # what changed between two interchanges
vendor/bin/edifact --version                       # installed version, as JSON
vendor/bin/edifact parse order.edi --pretty        # readable JSON

cat order.edi | vendor/bin/edifact inspect         # reads stdin when no path is given
```

Contract, so output can be consumed without guessing:

- **JSON results on stdout, parse and usage errors on stderr.** Validation diagnostics are part of the JSON result, so `| jq` works for reports too.
- **exit codes**: `0` success/valid, `1` invalid input, `2` usage error
- JSON by default; `--pretty` is purely cosmetic

No console framework is pulled in - a parsing library should not put one in your `vendor/`.

`parse` returns `globalSegments` (file-level segments such as `UNB` and `UNZ`), `messages` in document order, and `functionalGroups`. Each group includes its `UNG` header, `UNE` trailer, and zero-based `messageIndexes` into `messages`.

---

## Reading data

### Typed accessors

Typed segments expose their fields as methods - self-documenting and IDE-friendly:

```php
// NAD (Name & Address)
$nad->partyQualifier();  // 'BY'
$nad->name();            // 'ACME Corporation'
$nad->street();
$nad->city();
$nad->postalCode();
$nad->countryCode();     // ISO 3166-1 alpha-2

// QTY / PRI - with numeric conversion
$qty->quantityAsFloat(); // float
$qty->measureUnit();     // 'PCE', 'KGM', …
$pri->priceAsFloat();    // float

// DTM - with date parsing
$dtm->asDateTime();      // DateTimeImmutable|null
```

Every segment also exposes the raw structure when you need it:

```php
$segment->tag();          // 'NAD'
$segment->subId();        // 'BY'
$segment->rawValues();    // ['NAD', 'BY', ['0410106314', '160', 'Z12'], …]
```

### Accessing segments

```php
// Typed lookup: returns ?NADNameAddress, so PHPStan, Psalm and your IDE know `name()`
$buyer = $message->segmentOfType(NADNameAddress::class, 'BY');
$buyer?->name(); // always null-check: not every message has a buyer

// Fastest single lookup, by tag + subId, typed as ?SegmentInterface
$rawBuyer = $message->segmentByTagAndSubId('NAD', 'BY');

// All segments with a tag (keyed by subId)
$allNad = $message->segmentsByTag('NAD');

// Presence and counts, answered from an index built once per message
$message->has('QTY');       // bool
$message->countByTag();     // ['UNH' => 1, 'NAD' => 2, 'LIN' => 40, …]
count($message);            // total segments, duplicates included

// A message is iterable in document order, so it goes straight into anything
// expecting iterable<SegmentInterface>
foreach ($message as $segment) { … }
```

### Dumping a message

`toArray()` / `toJson()` render a whole message - or a single segment - as plain data,
with context children nested. Useful for logs, snapshot tests and diffing interchanges:

```php
$message->toArray();  // ['type' => 'ORDERS', 'segments' => [['tag' => 'UNH', 'subId' => '1', …], …]]
$message->toJson();   // pretty-printed JSON of the same structure

$segment->toArray();  // ['tag' => 'NAD', 'subId' => 'BY', 'rawValues' => [...]]
```

### Fluent query API

Chain filters and transformations over **every** segment (order preserved, duplicates
included):

```php
use EdifactParser\Segments\MOAMonetaryAmount;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\PRIPrice;

// Filter
$message->query()->withTag('NAD')->withSubId('CN')->get();
$message->query()->withTags(['NAD', 'LIN'])->get();
$message->query()->withoutTags(['UNH', 'UNT'])->get();
$message->query()->ofType(NADNameAddress::class)->get();
$message->query()->ofType(PRIPrice::class)->where(fn($price) => $price->priceAsFloat() > 1000)->get();

// Chain + paginate
$message->query()
    ->ofType(NADNameAddress::class)->withSubId('SU')
    ->where(fn($party) => $party->countryCode() === 'DE')
    ->limit(10)->skip(0)->get();

// Transform / inspect
$message->query()->ofType(NADNameAddress::class)->map(fn($party) => $party->name());
$message->query()->ofType(MOAMonetaryAmount::class)->reduce(fn(float $total, $amount) => $total + $amount->amountAsFloat(), 0.0);
$message->query()->withTags(['QTY', 'PRI'])->groupByTag();  // ['QTY' => [...], 'PRI' => [...]]
$message->query()->countByTag();              // ['NAD' => 2, 'LIN' => 40, …]
$message->query()->withTag('NAD')->first();   // ?SegmentInterface
$message->query()->withTag('NAD')->count();
$message->query()->withTag('UNS')->exists();  // bool

// A query is countable and iterable - no ->get() needed to loop
foreach ($message->query()->ofType(NADNameAddress::class) as $party) { … }
```

> `query()` and `$message->segments()` return **every** segment in original order,
> duplicates included. The keyed lookups (`segmentByTagAndSubId()`, `allSegments()`) index
> by tag + subId and keep the **last** occurrence - use the query API when duplicates matter.

### Line items

Line items group each `LIN` with its related detail segments (`QTY`, `PRI`, `PIA`, …). This is useful for orders and invoices:

```php
use EdifactParser\Segments\LINLineItem;
use EdifactParser\Segments\QTYQuantity;

foreach ($message->lineItems() as $lineItem) {
    $lin = $lineItem->segmentOfType(LINLineItem::class, '1');
    $qty = $lineItem->segmentOfType(QTYQuantity::class, '21');

    echo $lin?->itemNumber();      // product identifier
    echo $qty?->quantityAsFloat();

    count($lineItem);                       // segments in this line item
    foreach ($lineItem as $segment) { … }   // …or iterate them
}
```

### Hierarchical context segments

Context segments preserve parent → child relationships (e.g. `NAD → CTA → COM`):

```php
foreach ($message->contextSegments() as $context) {
    if ($context->tag() === 'NAD') {
        foreach ($context as $child) {          // …or ->children()
            // $child->tag(), $child->rawValues(), …
        }

        $context->childByTag('CTA');    // ?SegmentInterface - the first one
        $context->childrenByTag('COM'); // list<SegmentInterface> - all of them
        $context->hasChildren();        // bool
        count($context);                // number of children
        $context->toArray();            // the segment with its children nested
    }
}
```

Keyed lookups hand back the **typed** segment. Read it normally, then ask the message what was grouped under it:

```php
$buyer = $message->segmentOfType(NADNameAddress::class, 'BY');
$buyer?->name();

if ($buyer !== null) {
    $message->childrenOf($buyer); // CTA/COM children under this NAD
    $message->contextFor($buyer); // ?ContextSegment
}
```

### Interchange & envelope metadata

Every envelope segment exposes typed metadata:

```php
use EdifactParser\Segments\BGMBeginningOfMessage;
use EdifactParser\Segments\UNBInterchangeHeader;
use EdifactParser\Segments\UNTMessageFooter;
use EdifactParser\Segments\UNZInterchangeTrailer;

$unb = $result->globalSegments()->segmentOfType(UNBInterchangeHeader::class, 'UNOC');
$unb?->syntaxIdentifier();            // 'UNOC'
$unb?->senderIdentification();
$unb?->recipientIdentification();
$unb?->preparationDate();             // 'YYMMDD'
$unb?->interchangeControlReference();

$unz = $result->globalSegments()->segmentOfType(UNZInterchangeTrailer::class, '1');
$unz?->interchangeControlCount();     // number of messages/groups

$unt = $message->query()->ofType(UNTMessageFooter::class)->first();
$unt?->segmentCount();
$bgm = $message->query()->ofType(BGMBeginningOfMessage::class)->first();
$bgm?->documentCode(); // '220' for an order
```

### Functional groups (UNG/UNE)

When an interchange wraps messages in `UNG…UNE` groups, read them directly. Interchanges
without groups return an empty list - messages stay available flat via
`transactionMessages()`:

```php
foreach ($result->functionalGroups() as $group) {
    $group->messageType();               // e.g. 'ORDERS' (from the UNG)
    $group->header()->groupReference();
    $group->trailer()?->controlCount();

    foreach ($group as $message) {  // …or ->messages()
        // …
    }

    count($group);                  // messages in the group
}
```

### Statistics & analysis

`MessageAnalyzer` extracts counts and aggregates:

```php
use EdifactParser\Analysis\MessageAnalyzer;

$analyzer = new MessageAnalyzer($message);

$analyzer->getType();                     // 'ORDERS'
$analyzer->segmentCount();
$analyzer->lineItemCount();
$analyzer->segmentCountByTag('QTY');
$analyzer->getPartyQualifiers();          // ['BY', 'SU', 'CN'] (unique)
$analyzer->getCurrencies();               // ['EUR']
$analyzer->calculateTotalAmount('125');   // sum MOA with qualifier 125
$analyzer->calculateTotalQuantity('21');  // sum ordered quantities
$analyzer->hasSummarySection();           // UNS present?
$analyzer->getSummary();                  // array of the above
```

### Qualifier constants

Avoid magic strings with typed qualifier catalogs (IDE autocomplete, usable in `match`):

```php
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\Qualifier\NADQualifier;

$message->query()
    ->ofType(NADNameAddress::class)
    ->where(fn($party) => $party->partyQualifier() === NADQualifier::BUYER) // 'BY'
    ->get();
```

| Class | Covers |
|-------|--------|
| `NADQualifier` | Party roles - `BY`, `SU`, `CN`, `CZ`, `DP`, `IV`, `PR`, `CA`, `FW`, `MF`, `UC`, `WH` |
| `QTYQualifier` | Quantity types - `1`, `3`, `11`, `12`, `21`, `33`, `46`, `47`, `48`, `192` |
| `PRIQualifier` | Price types - `AAA`, `AAB`, `AAE`, `AAF`, `AAG`, `CAL`, `CT`, `DIS`, `LIS`, `MIN`, `RRP` |
| `DTMQualifier` | Date/time types - `137`, `2`, `3`, `4`, `10`, `11`, `13`, … |
| `RFFQualifier` | Reference types - `ON`, `IV`, `DQ`, `CU`, `SRN`, `CT`, `POR`, … |

### Character sets

The parser reads raw bytes. Decode non-ASCII values to UTF-8 from the interchange's syntax
identifier:

```php
use EdifactParser\Charset\Charset;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\UNBInterchangeHeader;

$unb = $result->globalSegments()->segmentOfType(UNBInterchangeHeader::class, 'UNOC');
$buyer = $message->segmentOfType(NADNameAddress::class, 'BY');
$unb?->characterEncoding();                              // 'ISO-8859-1'
if ($unb !== null && $buyer !== null) {
    $name = Charset::toUtf8($buyer->name(), $unb->syntaxIdentifier());
}
```

`UNOA` and `UNOB` use ASCII. `UNOC` through `UNOK` use ISO-8859 encodings. `UNOY` uses UTF-8.

### Built-in segments

32 segments are typed and registered by default (134 with
`SegmentFactory::withDirectorySegments()`):

- **Envelope / service:** `UNB`, `UNG`, `UNH`, `UNS`, `UNT`, `UNE`, `UNZ`
- **Header:** `BGM`, `DTM`, `RFF`, `NAD`, `CUX`, `TDT`, `LOC`, `FTX`
- **Party / terms:** `CTA`, `COM`, `PAT`, `PCD`, `TAX`, `TOD`
- **Detail / summary:** `LIN`, `PIA`, `IMD`, `QTY`, `PRI`, `MEA`, `PAC`, `GID`, `MOA`, `PCI`, `CNT`

Any other tag parses as an `UnknownSegment` (readable via `rawValues()`); add your own typed
class in a few lines. See [Extending](#extending).

---

## Writing EDIFACT

### Build individual segments

Fluent, type-safe builders produce segment objects:

```php
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\Qualifier\NADQualifier;

$nad = NADNameAddress::builder()
    ->withQualifier(NADQualifier::BUYER)
    ->withPartyId('123456')
    ->withName('ACME Corporation')
    ->withCity('Springfield')
    ->withCountryCode('US')
    ->build();
```

`NADNameAddress`, `QTYQuantity` and `PRIPrice` provide `::builder()`.

### Serialize segments to a string

`EdifactSerializer` renders parsed segments back to EDIFACT and escapes separators and release characters:

```php
use EdifactParser\Serializer\EdifactSerializer;
use EdifactParser\Serializer\UnaSeparators;

$serializer = new EdifactSerializer();
echo $serializer->serializeSegment($nad);
// NAD+BY+123456++ACME Corporation++Springfield+++US'

// A TransactionMessage is iterable, so it can be serialized directly
$edi = $serializer->serialize($message, includeUna: true);

// Custom delimiters
new EdifactSerializer(new UnaSeparators(component: '#', element: '|'));
```

### Assemble a full interchange

`InterchangeBuilder` writes a complete `UNB…UNZ` interchange and **fills in the UNT segment
counts and the UNZ control count automatically**:

```php
use EdifactParser\Writer\InterchangeBuilder;
use EdifactParser\Writer\MessageBuilder;
use EdifactParser\Segments\BGMBeginningOfMessage;

$bgm = new BGMBeginningOfMessage(['BGM', '220', 'PO-123', '9']);

$edi = InterchangeBuilder::create('SENDER', 'RECIPIENT', 'REF1')
    ->preparedAt('200101', '1200')
    ->addMessage(
        MessageBuilder::create('1', 'ORDERS')
            ->addSegment($bgm)
            ->addSegment($nad)
    )
    ->toString(); // ready-to-send EDIFACT string
```

---

## Validation

Check a message against a pluggable rule set - required segments, cardinality, and relative
order. The validator never throws; an empty result means the message conforms:

```php
use EdifactParser\Validation\MessageRuleSet;
use EdifactParser\Validation\MessageValidator;

$rules = MessageRuleSet::forType('ORDERS')
    ->require('UNH', 'BGM', 'UNT')       // mandatory segments
    ->occurs('NAD', 1, 5)                // between 1 and 5 NAD segments
    ->occurs('LIN', 1)                   // at least 1 line item
    ->inSequence('UNH', 'BGM', 'UNT');   // relative order of these tags

$validator = new MessageValidator();

foreach ($validator->validate($message, $rules) as $violation) {
    echo "{$violation->segmentTag()}: {$violation->message()}\n";
}

$validator->isValid($message, $rules); // bool
```

Ready-made rule sets for common message types are provided as starting points:

```php
use EdifactParser\Validation\MessageRuleSets;

$validator->validate($message, MessageRuleSets::orders()); // orders(), invoic(), desadv(), iftmin()
```

---

## Extending

### Custom segments

Extend `AbstractSegment` and register your class. The shared accessor helpers
(`element()`, `component()`, `firstComponent()`) safely read simple and composite elements:

```php
namespace YourApp\Segments;

use EdifactParser\Segments\AbstractSegment;

/** @psalm-immutable */
final class EQDEquipmentDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'EQD';
    }

    // EQD+CN+ABCU1234567+22G1
    public function equipmentQualifier(): string
    {
        return $this->element(1);        // 'CN'
    }

    public function equipmentId(): string
    {
        return $this->firstComponent(2); // 'ABCU1234567'
    }
}
```

```php
use EdifactParser\EdifactParser;
use EdifactParser\Segments\SegmentFactory;
use YourApp\Segments\EQDEquipmentDetails;

$factory = SegmentFactory::withAdditionalSegments([
    'EQD' => EQDEquipmentDetails::class, // added on top of the 32 built-ins
]);

$parser = new EdifactParser($factory);
```

> `withAdditionalSegments()` keeps every default and merges your tags on top.
> Registering a custom class under a default tag overrides that default. Use
> `withSegments()` instead when you want an explicit, closed set of segments.

### Introspection

Ask the factory what it knows, instead of reading the source:

```php
$factory = SegmentFactory::withDefaultSegments();

$factory->registeredTags();        // ['BGM', 'CNT', 'COM', … ] - 32 tags, sorted
$factory->classForTag('NAD');      // EdifactParser\Segments\NADNameAddress
$factory->classForTag('ZZZ');      // null - would become an UnknownSegment

$factory->describeTag('QTY')?->accessors();
// ['measureUnit' => 'string', 'qualifier' => 'string',
//  'quantity' => 'string', 'quantityAsFloat' => 'float']
```

Descriptors are derived by reflection, so they cannot drift from the code.
`registeredTags()` and `classForTag()` read the map only - no class is loaded.

The shape of `toArray()`/`toJson()` is published as a JSON Schema at
[`schema/message.schema.json`](schema/message.schema.json), and a test asserts the schema
still matches what a parsed message actually produces.

### Composable segment bundles

The defaults are exposed as two composable bundles so you can build a lean factory
that only types the tags you care about - everything else still parses as a readable
`UnknownSegment`:

- `SegmentFactory::ENVELOPE_SEGMENTS` - the UN* service/control segments (7).
- `SegmentFactory::BUSINESS_SEGMENTS` - header, party/terms, detail and summary (25).
- `SegmentFactory::DEFAULT_SEGMENTS` - the union of both (32).

```php
// Envelope structure + just the segments you extract:
$factory = SegmentFactory::withSegments(
    SegmentFactory::ENVELOPE_SEGMENTS + [
        'NAD' => NADNameAddress::class,
        'LIN' => LINLineItem::class,
    ],
);
```

### Custom grouping rules

Context hierarchies and line-item boundaries are driven by `GroupingRules`. Pass a
customized instance to change which tags open a context, attach as children, or close a
line-item section:

```php
use EdifactParser\EdifactParser;
use EdifactParser\GroupingRules;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\StreamingParser;

$rules = GroupingRules::default()
    ->withContextTags(['NAD', 'LIN'])
    ->withChildTags(['CTA', 'COM', 'DTM'])
    ->withBreakLineItemTags(['UNS', 'CNT', 'UNT']);

$parser = new EdifactParser(SegmentFactory::withDefaultSegments(), $rules);

// …or, when the default segments are all you need:
$parser = EdifactParser::createWithDefaultSegments($rules);
$stream = StreamingParser::createWithDefaultSegments($rules);
```

`GroupingRules` also reads back what it is configured with - `contextTags()`,
`childTags()`, `breakLineItemTags()` - and the defaults are exposed as
`GroupingRules::DEFAULT_CONTEXT_TAGS`, `DEFAULT_CHILD_TAGS` and
`DEFAULT_BREAK_LINE_ITEM_TAGS`.

More examples in [`example/`](example): [extracting data](example/extracting-data.php),
[query filtering](example/query-filtering.php),
[printing segments](example/printing-segments.php),
[context segments](example/context-segments.php).

---

## Debugging

```php
$segment->toArray(); // ['tag' => 'NAD', 'subId' => 'CN', 'rawValues' => [...]]
$segment->toJson();  // pretty-printed JSON

$message->toArray(); // ['type' => 'ORDERS', 'segments' => [...]] - contexts nested
$message->toJson();
```

### Error handling

```php
use EdifactParser\EdifactParser;
use EdifactParser\Exception\InvalidFile;

$parser = EdifactParser::createWithDefaultSegments();
try {
    $result = $parser->parseFile('invalid.edi');
} catch (InvalidFile $e) {
    $e->getErrors();   // parser errors, as strings
    $e->getContext();  // extra context, formatted into getMessage()
}
```

### Structured diagnostics

Matching on English prose is fragile, so parse failures and validation failures share one
type with **stable codes** and, where known, a position:

```php
use EdifactParser\Diagnostics\DiagnosticCode;
use EdifactParser\EdifactParser;
use EdifactParser\Exception\InvalidFile;

$parser = EdifactParser::createWithDefaultSegments();
try {
    $parser->parse("UNH+1+ORDERS'NAD+BY");
} catch (InvalidFile $e) {
    foreach ($e->getDiagnostics() as $d) {
        $d->code();          // 'segment.unterminated' - stable, match on this
        $d->severity();      // 'error' | 'warning'
        $d->segmentIndex();  // 2
        $d->tag();           // 'NAD'
        $d->elementPath();   // 'C186/6060', when known
        $d->toArray();       // JSON-serialisable
        (string) $d;         // error [segment.unterminated] at segment 2 (NAD): …
    }
}

// The validator speaks the same vocabulary
$diagnostics = (new MessageValidator())->diagnose($message, MessageRuleSets::orders());
if ($diagnostics !== []) {
    $diagnostics[0]->code() === DiagnosticCode::SEGMENT_REQUIRED;
}
```

Codes are public API - see `DiagnosticCode` for the catalogue. Messages are not: they may
be reworded at any time.

---

## Development

```bash
composer install         # also installs Psalm into vendor-bin/psalm
composer test            # everything CI gates on: quality checks, unit and functional tests
composer quality         # PHP-CS-Fixer, Psalm, PHPStan, Rector (dry runs)
composer fix             # apply PHP-CS-Fixer and Rector fixes
composer examples        # run every example/ script with assertions on
composer coverage        # full suite, failing below 100% line coverage (needs pcov or xdebug)
composer bench           # benchmark the hot paths (corpus generated at runtime)
composer verify-package  # build the dist, install it elsewhere, and use it
composer list            # every script, with a description
```

Run `composer test` and `composer examples` before opening a PR. CI also runs the 100% coverage gate, benchmarks, and the published package check. Local coverage needs `pcov` or `xdebug`.

Psalm lives in its own Composer project under `vendor-bin/psalm`. The library resolves its
dependencies against PHP 8.0, which would pin Psalm to a release that crashes on current
PHP. Isolating it lets the analysis run on any PHP from 8.2 up, while `psalm.xml` still
targets 8.0.

CI runs the benchmarks on every pull request, measuring the base branch and the head
branch on the same runner and failing when a metric regresses beyond 1.5×. Absolute
timings on shared hardware mean little; ratios measured back to back do. Never change
`tools/benchmark.php` in a commit that also reports a performance delta - the numbers
stop being comparable.

- PHP 8.0+, strict types, PSR-4. Type hints and tests required for new functionality.
- All code must pass PHP-CS-Fixer, Psalm, PHPStan and Rector (CI is authoritative).

---

## Contributing

Contributions of all kinds are welcome: bug fixes, ideas, and improvements.

- [Report an issue](https://github.com/Chemaclass/EdifactParser/issues)
- [Open a pull request](https://github.com/Chemaclass/EdifactParser/pulls)

See the [contributing guide](.github/CONTRIBUTING.md) to get started.
