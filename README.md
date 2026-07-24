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
  and trivially [extensible](#-extending) with your own.
- 🔎 **Fluent query API** and a **statistics analyzer** for extracting data.
- 🌍 **Charset-aware** (`UNOA`…`UNOY`), **strictly typed** (PHP 8.0+, PSR-4), and fully
  covered by PHPUnit, PHPStan, Psalm, Rector and PHP-CS-Fixer.

## Table of Contents

- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Parsing](#-parsing) · [Streaming large files](#streaming-large-files)
- [Reading data](#-reading-data) · [Typed accessors](#typed-accessors) · [Query API](#fluent-query-api) · [Line items](#line-items) · [Context hierarchy](#hierarchical-context-segments) · [Envelope metadata](#interchange--envelope-metadata) · [Functional groups](#functional-groups-ungune) · [Statistics](#statistics--analysis) · [Qualifier constants](#qualifier-constants) · [Character sets](#character-sets) · [Built-in segments](#built-in-segments)
- [Writing EDIFACT](#-writing-edifact)
- [Validation](#-validation)
- [Extending](#-extending)
- [Debugging](#-debugging)
- [Development](#-development)
- [Contributing](#-contributing)

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
```

A **message** starts at `UNH` and ends at `UNT`; an **interchange** wraps messages between
`UNB` and `UNZ`, optionally grouped by `UNG`/`UNE`. Invalid input throws
[`InvalidFile`](#error-handling).

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
```

### Fluent query API

Chain filters and transformations over **every** segment (order preserved, duplicates
included):

```php
// Filter
$message->query()->withTag('NAD')->withSubId('CN')->get();
$message->query()->withTags(['NAD', 'LIN'])->get();
$message->query()->ofType(NADNameAddress::class)->get();
$message->query()->withTag('PRI')->where(fn($s) => $s->priceAsFloat() > 1000)->get();

// Chain + paginate
$message->query()
    ->withTag('NAD')->withSubId('SU')
    ->where(fn($s) => $s->countryCode() === 'DE')
    ->limit(10)->skip(0)->get();

// Transform / inspect
$message->query()->withTag('NAD')->map(fn($s) => $s->name());
$message->query()->withTag('NAD')->first();   // ?SegmentInterface
$message->query()->withTag('NAD')->count();
$message->query()->withTag('UNS')->exists();  // bool
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
}
```

### Hierarchical context segments

Context segments preserve parent → child relationships (e.g. `NAD → CTA → COM`):

```php
foreach ($message->contextSegments() as $context) {
    if ($context->tag() === 'NAD') {
        foreach ($context->children() as $child) {
            // $child->tag(), $child->rawValues(), …
        }
    }
}
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

    foreach ($group->messages() as $message) {
        // …
    }
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

32 segments are typed and registered by default:

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

$rules = GroupingRules::default()
    ->withContextTags(['NAD', 'LIN'])
    ->withChildTags(['CTA', 'COM', 'DTM'])
    ->withBreakLineItemTags(['UNS', 'CNT', 'UNT']);

$parser = new EdifactParser(SegmentFactory::withDefaultSegments(), $rules);
```

More examples in [`example/`](example): [extracting data](example/extracting-data.php),
[query filtering](example/query-filtering.php),
[printing segments](example/printing-segments.php),
[context segments](example/context-segments.php).

---

## 🐛 Debugging

```php
$segment->toArray(); // ['tag' => 'NAD', 'subId' => 'CN', 'rawValues' => [...]]
$segment->toJson();  // pretty-printed JSON
```

### Error handling

```php
use EdifactParser\Exception\InvalidFile;

try {
    $result = $parser->parseFile('invalid.edi');
} catch (InvalidFile $e) {
    $e->getErrors();   // parser errors
    $e->getContext();  // extra context, formatted into getMessage()
}
```

---

## 🛠️ Development

```bash
composer install
composer test       # PHPUnit (unit + functional)
composer quality    # PHP-CS-Fixer, Psalm, PHPStan, Rector
composer csfix      # apply code-style fixes
```

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
