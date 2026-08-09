# 📦 EDIFACT Parser

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/?branch=main)
[![Type Coverage](https://shepherd.dev/github/Chemaclass/EdifactParser/coverage.svg)](https://shepherd.dev/github/chemaclass/EdifactParser)
[![CI](https://github.com/Chemaclass/EdifactParser/workflows/CI/badge.svg?branch=main)](https://github.com/Chemaclass/EdifactParser/actions)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)

A complete **PHP toolkit for UN/EDIFACT** — read, write, validate, and stream EDI
interchanges with a typed, object-oriented API.

> **EDIFACT** — _Electronic Data Interchange For Administration, Commerce, and Transport_ —
> is the international standard for structured business documents (orders, invoices,
> despatch advices, transport instructions). 🔍 New to it? [Start here](/docu/README.md).

## Why this library

- 📥 **Parse** any interchange — unknown tags degrade gracefully to raw values, so no
  message type is unsupported.
- 📤 **Write** it back — serialize segments to a valid `.edi` string, or assemble a full
  `UNB…UNZ` interchange with **auto-computed control counts**.
- ✅ **Validate** — pluggable rule sets for required segments, cardinality, and order.
- 🌊 **Stream** — parse multi-gigabyte files in bounded memory (one message at a time).
- 🧱 **Model the full envelope** — interchange → functional groups (`UNG/UNE`) → messages,
  with duplicate-preserving access and typed metadata on every envelope segment.
- 🏷️ **32 typed segments** out of the box with domain accessors, plus qualifier constants —
  or **134** with the directory bundle, and trivially [extensible](#-extending) with your own.
- 📐 **Directory-aware** — validate elements, lengths, representations and code lists against
  the real UN/EDIFACT directories, and group messages into their actual nested segment
  groups (`SG1…SGn`) rather than a heuristic.
- 🖥️ **CLI included** — `edifact parse|inspect|validate|segments`, JSON on stdout, documented
  exit codes.
- 🩺 **Structured diagnostics** — stable codes and positions across parsing and validation,
  so failures are matched on, not string-searched.
- 🔎 **Fluent query API** and a **statistics analyzer** for extracting data. Results,
  messages, line items and queries are all `Countable` and `iterable`, and any of them
  dumps to plain arrays or JSON.
- 🌍 **Charset-aware** (`UNOA`…`UNOY`), **strictly typed** (PHP 8.0+, PSR-4), and fully
  covered by PHPUnit, PHPStan, Psalm, Rector and PHP-CS-Fixer.

## Table of Contents

- [Installation](#-installation) · [Upgrading from 6.x](UPGRADING.md)
- [Command line](#-command-line)
- [Quick Start](#-quick-start)
- [Parsing](#-parsing) · [Streaming large files](#streaming-large-files)
- [Reading data](#-reading-data) · [Typed accessors](#typed-accessors) · [Dumping a message](#dumping-a-message) · [Query API](#fluent-query-api) · [Line items](#line-items) · [Context hierarchy](#hierarchical-context-segments) · [Envelope metadata](#interchange--envelope-metadata) · [Functional groups](#functional-groups-ungune) · [Statistics](#statistics--analysis) · [Qualifier constants](#qualifier-constants) · [Character sets](#character-sets) · [Built-in segments](#built-in-segments)
- [Writing EDIFACT](#-writing-edifact)
- [Validation](#-validation)
- [Extending](#-extending)
- [Debugging](#-debugging)
- [Development](#-development)
- [Contributing](#-contributing)

For AI coding agents: [llms.txt](llms.txt) and [docs/llms/](docs/llms). Every snippet there
is backed by a runnable file under [`example/`](example) that CI executes.

---

## 💾 Installation

```bash
composer require chemaclass/edifact-parser
```

Requires PHP 8.0+ with `ext-json` and `ext-mbstring`.

---

## 🚀 Quick Start

```php
<?php declare(strict_types=1);

use EdifactParser\EdifactParser;

require 'vendor/autoload.php';

$result = EdifactParser::createWithDefaultSegments()
    ->parseFile('/path/to/order.edi'); // or ->parse($ediString)

foreach ($result->transactionMessages() as $message) {
    echo $message->messageType();      // 'ORDERS', 'INVOIC', 'IFTMIN', …

    // Typed accessors — no magic array indices
    $buyer = $message->segmentByTagAndSubId('NAD', 'BY');
    echo $buyer?->name();              // 'ACME Corporation'
    echo $buyer?->countryCode();       // 'DE'

    foreach ($message->lineItems() as $lineItem) {
        $qty = $lineItem->segmentByTagAndSubId('QTY', '21');
        echo $qty?->quantityAsFloat(); // 100.0
    }
}
```

The parser never throws on unknown segments — they become `UnknownSegment`s you can still
read via `rawValues()`, so you can process any interchange and add typed segments later.

---

## 📥 Parsing

`EdifactParser::parse()` / `parseFile()` return a `ParserResult`:

```php
$result = EdifactParser::createWithDefaultSegments()->parse($ediString);

$result->transactionMessages();  // list<TransactionMessage> — the UNH…UNT blocks
$result->functionalGroups();     // list<FunctionalGroup>     — UNG…UNE groups, if any
$result->globalSegments();       // TransactionMessage        — file-level UNA/UNB/UNZ

$result->firstMessage();             // ?TransactionMessage
$result->messagesOfType('INVOIC');   // list<TransactionMessage> — an interchange may mix types
count($result);                      // number of messages
foreach ($result as $message) { … }  // iterate the messages directly
```

A **message** starts at `UNH` and ends at `UNT`; an **interchange** wraps messages between
`UNB` and `UNZ`, optionally grouped by `UNG`/`UNE`. Invalid input throws
[`InvalidFile`](#error-handling).

### Choosing a tokenizer

Turning raw text into segments is pluggable. **`NativeTokenizer` is the default** — a
regex-free single-pass scanner, ~1.8× faster at tokenizing (~1.3× on `parse()` overall),
and it never rewrites the bytes it reads:

```php
$parser = EdifactParser::createWithDefaultSegments();   // NativeTokenizer
```

`SabasTokenizer` delegates to `sabas/edifact`, which was the default up to 6.x. It is still
available, but be aware it **strips every byte in `\x80-\xFF`** — `NAD+BY+++Müller` comes
back as `Mller`:

```php
use EdifactParser\Tokenizer\SabasTokenizer;

new EdifactParser(SegmentFactory::withDefaultSegments(), tokenizer: new SabasTokenizer());
```

Reach for it when you need bug-for-bug compatibility with 6.x, or want the restricted
`UNOB` repertoire enforced. Otherwise the default is both faster and lossless.

For ASCII input the two tokenize identically — verified segment-for-segment across the test
fixtures and a generated corpus.

---

### Streaming large files

Stream messages one at a time in **bounded memory** — ideal for large interchanges. A
leading `UNA` service-string advice (custom separators/release char) is honoured
automatically:

```php
use EdifactParser\StreamingParser;

foreach (StreamingParser::createWithDefaultSegments()->parseFile('/path/to/large.edi') as $message) {
    process($message); // only one message is held in memory at a time
}
```

---

## 🖥️ Command line

`composer require` installs an `edifact` binary. It answers "what is in this file?" without
writing a script — and it is built for automation as much as for people:

```bash
edifact parse order.edi                 # the parsed interchange as JSON
edifact inspect order.edi               # type, counts by tag, line items
edifact validate order.edi              # rule set picked from the message type
edifact validate order.edi --rules=ORDERS
edifact segments                        # every tag the parser knows
edifact segments --tag=NAD              # its accessors and return types
edifact parse order.edi --pretty        # readable JSON

cat order.edi | edifact inspect         # reads stdin when no path is given
```

Contract, so output can be consumed without guessing:

- **data on stdout, diagnostics on stderr** — never interleaved, so `| jq` always works
- **exit codes**: `0` success/valid, `1` invalid input, `2` usage error
- JSON by default; `--pretty` is purely cosmetic

No console framework is pulled in — a parsing library should not put one in your `vendor/`.

---

## 📖 Reading data

### Typed accessors

Typed segments expose their fields as methods — self-documenting and IDE-friendly:

```php
// NAD (Name & Address)
$nad->partyQualifier();  // 'BY'
$nad->name();            // 'ACME Corporation'
$nad->street();
$nad->city();
$nad->postalCode();
$nad->countryCode();     // ISO 3166-1 alpha-2

// QTY / PRI — with numeric conversion
$qty->quantityAsFloat(); // float
$qty->measureUnit();     // 'PCE', 'KGM', …
$pri->priceAsFloat();    // float

// DTM — with date parsing
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
// Fastest single lookup, by tag + subId
$nad = $message->segmentByTagAndSubId('NAD', 'BY'); // ?SegmentInterface

// All segments with a tag (keyed by subId)
$allNad = $message->segmentsByTag('NAD');

$nad?->name(); // always null-check — not every segment exists in every message

// Presence and counts, answered from an index built once per message
$message->has('QTY');       // bool
$message->countByTag();     // ['UNH' => 1, 'NAD' => 2, 'LIN' => 40, …]
count($message);            // total segments, duplicates included

// A message is iterable in document order, so it goes straight into anything
// expecting iterable<SegmentInterface>
foreach ($message as $segment) { … }
```

### Dumping a message

`toArray()` / `toJson()` render a whole message — or a single segment — as plain data,
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
// Filter
$message->query()->withTag('NAD')->withSubId('CN')->get();
$message->query()->withTags(['NAD', 'LIN'])->get();
$message->query()->withoutTags(['UNH', 'UNT'])->get();
$message->query()->ofType(NADNameAddress::class)->get();
$message->query()->withTag('PRI')->where(fn($s) => $s->priceAsFloat() > 1000)->get();

// Chain + paginate
$message->query()
    ->withTag('NAD')->withSubId('SU')
    ->where(fn($s) => $s->countryCode() === 'DE')
    ->limit(10)->skip(0)->get();

// Transform / inspect
$message->query()->withTag('NAD')->map(fn($s) => $s->name());
$message->query()->withTag('MOA')->reduce(fn(float $t, $s) => $t + $s->amountAsFloat(), 0.0);
$message->query()->withTags(['QTY', 'PRI'])->groupByTag();  // ['QTY' => [...], 'PRI' => [...]]
$message->query()->countByTag();              // ['NAD' => 2, 'LIN' => 40, …]
$message->query()->withTag('NAD')->first();   // ?SegmentInterface
$message->query()->withTag('NAD')->count();
$message->query()->withTag('UNS')->exists();  // bool

// A query is countable and iterable — no ->get() needed to loop
foreach ($message->query()->withTag('NAD') as $nad) { … }
```

> `query()` and `$message->segments()` return **every** segment in original order,
> duplicates included. The keyed lookups (`segmentByTagAndSubId()`, `allSegments()`) index
> by tag + subId and keep the **last** occurrence — use the query API when duplicates matter.

### Line items

Line items group each `LIN` with its related detail segments (`QTY`, `PRI`, `PIA`, …) —
ideal for orders and invoices:

```php
foreach ($message->lineItems() as $lineItem) {
    $lin = $lineItem->segmentByTagAndSubId('LIN', '1');
    $qty = $lineItem->segmentByTagAndSubId('QTY', '21');

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

        $context->childByTag('CTA');    // ?SegmentInterface — the first one
        $context->childrenByTag('COM'); // list<SegmentInterface> — all of them
        $context->hasChildren();        // bool
        count($context);                // number of children
        $context->toArray();            // the segment with its children nested
    }
}
```

Keyed lookups always hand back the **typed** segment, so you can go the other way too —
read a segment normally, then ask the message what was grouped under it:

```php
$buyer = $message->segmentByTagAndSubId('NAD', 'BY'); // NADNameAddress
$buyer?->name();                                       // typed accessors work

$message->childrenOf($buyer);  // list<SegmentInterface> — the CTA/COM under this NAD
$message->contextFor($buyer);  // ?ContextSegment — the same, as a context object
```

### Interchange & envelope metadata

Every envelope segment exposes typed metadata:

```php
$unb = $result->globalSegments()->segmentByTagAndSubId('UNB', 'UNOC');
$unb?->syntaxIdentifier();            // 'UNOC'
$unb?->senderIdentification();
$unb?->recipientIdentification();
$unb?->preparationDate();             // 'YYMMDD'
$unb?->interchangeControlReference();

$unz = $result->globalSegments()->segmentByTagAndSubId('UNZ', '1');
$unz?->interchangeControlCount();     // number of messages/groups

$unt = $message->query()->withTag('UNT')->first(); // segmentCount(), messageReferenceNumber()
$bgm = $message->query()->withTag('BGM')->first();  // documentCode() e.g. '220', documentNumber()
```

### Functional groups (UNG/UNE)

When an interchange wraps messages in `UNG…UNE` groups, read them directly. Interchanges
without groups return an empty list — messages stay available flat via
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
use EdifactParser\Segments\Qualifier\NADQualifier;

$message->query()
    ->withTag('NAD')
    ->where(fn($s) => $s->partyQualifier() === NADQualifier::BUYER) // 'BY'
    ->get();
```

| Class | Covers |
|-------|--------|
| `NADQualifier` | Party roles — `BY`, `SU`, `CN`, `CZ`, `DP`, `IV`, `PR`, `CA`, `FW`, `MF`, `UC`, `WH` |
| `QTYQualifier` | Quantity types — `1`, `3`, `11`, `12`, `21`, `33`, `46`, `47`, `48`, `192` |
| `PRIQualifier` | Price types — `AAA`, `AAB`, `AAE`, `AAF`, `AAG`, `CAL`, `CT`, `DIS`, `LIS`, `MIN`, `RRP` |
| `DTMQualifier` | Date/time types — `137`, `2`, `3`, `4`, `10`, `11`, `13`, … |
| `RFFQualifier` | Reference types — `ON`, `IV`, `DQ`, `CU`, `SRN`, `CT`, `POR`, … |

### Character sets

The parser reads raw bytes. Decode non-ASCII values to UTF-8 from the interchange's syntax
identifier:

```php
use EdifactParser\Charset\Charset;

$unb = $result->globalSegments()->segmentByTagAndSubId('UNB', 'UNOC');
$unb?->characterEncoding();                              // 'ISO-8859-1'
$name = Charset::toUtf8($nad->name(), $unb->syntaxIdentifier());
```

`UNOA`/`UNOB` → ASCII, `UNOC`–`UNOK` → ISO-8859-*, `UNOY` → UTF-8.

### Built-in segments

32 segments are typed and registered by default (134 with
`SegmentFactory::withDirectorySegments()`):

- **Envelope / service:** `UNB`, `UNG`, `UNH`, `UNS`, `UNT`, `UNE`, `UNZ`
- **Header:** `BGM`, `DTM`, `RFF`, `NAD`, `CUX`, `TDT`, `LOC`, `FTX`
- **Party / terms:** `CTA`, `COM`, `PAT`, `PCD`, `TAX`, `TOD`
- **Detail / summary:** `LIN`, `PIA`, `IMD`, `QTY`, `PRI`, `MEA`, `PAC`, `GID`, `MOA`, `PCI`, `CNT`

Any other tag parses as an `UnknownSegment` (readable via `rawValues()`); add your own typed
class in a few lines — see [Extending](#-extending).

---

## 📤 Writing EDIFACT

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

`EdifactSerializer` is the inverse of parsing — it round-trips a parsed interchange
byte-for-byte and escapes separators/release chars for you:

```php
use EdifactParser\Serializer\EdifactSerializer;
use EdifactParser\Serializer\UnaSeparators;

$serializer = new EdifactSerializer();
echo $serializer->serializeSegment($nad);
// NAD+BY+123456++ACME Corporation++Springfield+++US'

$edi = $serializer->serialize([$unh, $bgm, $nad, $unt], includeUna: true);

// A TransactionMessage is iterable, so it round-trips without unwrapping
$edi = $serializer->serialize($message);

// Custom delimiters
new EdifactSerializer(new UnaSeparators(component: '#', element: '|'));
```

### Assemble a full interchange

`InterchangeBuilder` writes a complete `UNB…UNZ` interchange and **fills in the UNT segment
counts and the UNZ control count automatically**:

```php
use EdifactParser\Writer\InterchangeBuilder;
use EdifactParser\Writer\MessageBuilder;

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

## ✅ Validation

Check a message against a pluggable rule set — required segments, cardinality, and relative
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

## 🔧 Extending

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

> `withAdditionalSegments()` keeps every default and merges your tags on top —
> registering a custom class under a default tag overrides that default. Use
> `withSegments()` instead when you want an explicit, closed set of segments.

### Introspection

Ask the factory what it knows, instead of reading the source:

```php
$factory = SegmentFactory::withDefaultSegments();

$factory->registeredTags();        // ['BGM', 'CNT', 'COM', … ] — 32 tags, sorted
$factory->classForTag('NAD');      // EdifactParser\Segments\NADNameAddress
$factory->classForTag('ZZZ');      // null — would become an UnknownSegment

$factory->describeTag('QTY')?->accessors();
// ['measureUnit' => 'string', 'qualifier' => 'string',
//  'quantity' => 'string', 'quantityAsFloat' => 'float']
```

Descriptors are derived by reflection, so they cannot drift from the code.
`registeredTags()` and `classForTag()` read the map only — no class is loaded.

The shape of `toArray()`/`toJson()` is published as a JSON Schema at
[`schema/message.schema.json`](schema/message.schema.json), and a test asserts the schema
still matches what a parsed message actually produces.

### Composable segment bundles

The defaults are exposed as two composable bundles so you can build a lean factory
that only types the tags you care about — everything else still parses as a readable
`UnknownSegment`:

- `SegmentFactory::ENVELOPE_SEGMENTS` — the UN* service/control segments (7).
- `SegmentFactory::BUSINESS_SEGMENTS` — header, party/terms, detail and summary (25).
- `SegmentFactory::DEFAULT_SEGMENTS` — the union of both (32).

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

`GroupingRules` also reads back what it is configured with — `contextTags()`,
`childTags()`, `breakLineItemTags()` — and the defaults are exposed as
`GroupingRules::DEFAULT_CONTEXT_TAGS`, `DEFAULT_CHILD_TAGS` and
`DEFAULT_BREAK_LINE_ITEM_TAGS`.

More examples in [`example/`](example): [extracting data](example/extracting-data.php),
[query filtering](example/query-filtering.php),
[printing segments](example/printing-segments.php),
[context segments](example/context-segments.php).

---

## 🐛 Debugging

```php
$segment->toArray(); // ['tag' => 'NAD', 'subId' => 'CN', 'rawValues' => [...]]
$segment->toJson();  // pretty-printed JSON

$message->toArray(); // ['type' => 'ORDERS', 'segments' => [...]] — contexts nested
$message->toJson();
```

### Error handling

```php
use EdifactParser\Exception\InvalidFile;

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

catch (InvalidFile $e) {
    foreach ($e->getDiagnostics() as $d) {
        $d->code();          // 'segment.unterminated' — stable, match on this
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
$violation->code() === DiagnosticCode::SEGMENT_REQUIRED;
```

Codes are public API — see `DiagnosticCode` for the catalogue. Messages are not: they may
be reworded at any time.

---

## 🛠️ Development

```bash
composer install
composer test       # PHPUnit (unit + functional)
composer quality    # PHP-CS-Fixer, Psalm, PHPStan, Rector
composer csfix      # apply code-style fixes
composer bench      # benchmark the hot paths (corpus generated at runtime)
composer verify-package  # build the dist, install it elsewhere, and use it
```

CI runs the benchmarks on every pull request, measuring the base branch and the head
branch on the same runner and failing when a metric regresses beyond 1.5×. Absolute
timings on shared hardware mean little; ratios measured back to back do. Never change
`tools/benchmark.php` in a commit that also reports a performance delta — the numbers
stop being comparable.

- PHP 8.0+, strict types, PSR-4. Type hints and tests required for new functionality.
- All code must pass PHP-CS-Fixer, Psalm, PHPStan and Rector (CI is authoritative).

> **Local toolchain note:** the pinned Psalm (`vimeo/psalm ^4.30`) runs on **PHP ≤ 8.3** —
> run it under 8.3 if your CLI is newer. On PHP > 8.3, PHP-CS-Fixer needs
> `PHP_CS_FIXER_IGNORE_ENV=1`.

---

## 🤝 Contributing

Contributions of all kinds are welcome — bug fixes, ideas, and improvements.

- 🐛 [Report an issue](https://github.com/Chemaclass/EdifactParser/issues)
- 🔧 [Open a pull request](https://github.com/Chemaclass/EdifactParser/pulls)

📋 See the [contributing guide](.github/CONTRIBUTING.md) to get started.
