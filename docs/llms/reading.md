# Reading data

Runnable version: [`example/llms-reading.php`](../../example/llms-reading.php)

## Keyed lookups return the typed segment

```php
$buyer = $message->segmentByTagAndSubId('NAD', 'BY');  // ?NADNameAddress
$buyer?->name();
$buyer?->countryCode();

$message->segmentsByTag('NAD');   // array<array-key, SegmentInterface>, keyed by subId
```

Two things to know:

- The result is the **typed** segment, so accessors and `instanceof` work.
- Keyed views keep the **last** segment for a given tag+subId. Use `query()` when
  duplicates matter.

## Typed accessors

```php
$nad->partyQualifier();   // 'BY'
$nad->name();
$nad->street();
$nad->city();
$nad->postalCode();
$nad->countryCode();

$qty->quantityAsFloat();  // float
$qty->measureUnit();      // 'PCE', 'KGM', …
$pri->priceAsFloat();
$dtm->asDateTime();       // ?DateTimeImmutable
```

Any segment also exposes the raw structure:

```php
$segment->tag();        // 'NAD'
$segment->subId();      // 'BY'
$segment->rawValues();  // ['NAD', 'BY', ['0410106314', '160', 'Z12'], …]
$segment->toArray();
```

Discover accessors without reading the source — see [extending](extending.md#introspection).

## Query API

Ordered, duplicate-preserving, immutable. Every filter returns a new query.

```php
$message->query()->withTag('NAD')->withSubId('BY')->get();
$message->query()->withTags(['NAD', 'LIN'])->get();
$message->query()->withoutTags(['UNH', 'UNT'])->get();
$message->query()->ofType(NADNameAddress::class)->get();
$message->query()->where(fn($s) => $s->tag() === 'QTY')->get();

$message->query()->withTag('NAD')->first();      // ?SegmentInterface
$message->query()->withTag('NAD')->last();
$message->query()->withTag('NAD')->count();
$message->query()->withTag('UNS')->exists();     // bool
$message->query()->withTag('NAD')->isEmpty();
$message->query()->withTag('NAD')->limit(10)->skip(2)->get();

$message->query()->withTag('NAD')->map(fn($s) => $s->name());
$message->query()->withTag('MOA')->reduce(fn(float $t, $s) => $t + $s->amountAsFloat(), 0.0);
$message->query()->groupByTag();    // array<string, list<SegmentInterface>>
$message->query()->countByTag();    // array<string, int>
$message->query()->each(fn($s) => null);

foreach ($message->query()->withTag('NAD') as $nad) { }   // countable + iterable
```

## Line items

Each `LIN` with the detail segments that follow it.

```php
foreach ($message->lineItems() as $lineItem) {
    $lineItem->segmentByTagAndSubId('LIN', '1');
    $lineItem->segmentByTagAndSubId('QTY', '21')?->quantityAsFloat();

    count($lineItem);
    foreach ($lineItem as $segment) { }
}

$message->lineItemById(1);   // ?LineItem — accepts string|int
```

## Context segments

A context is a parent segment plus what was grouped under it (`NAD` → `CTA`/`COM`,
`LIN` → `QTY`/`PRI`). Defaults come from `GroupingRules`.

```php
foreach ($message->contextSegments() as $context) {
    $context->tag();
    $context->children();
    $context->childByTag('CTA');      // ?SegmentInterface
    $context->childrenByTag('COM');   // list<SegmentInterface>
    $context->hasChildren();
    count($context);
    foreach ($context as $child) { }
    $context->toArray();              // segment with children nested
}

// From a segment you already hold:
$message->childrenOf($buyer);   // list<SegmentInterface>
$message->contextFor($buyer);   // ?ContextSegment
```

## Envelope metadata

```php
$unb = $result->globalSegments()->segmentByTagAndSubId('UNB', 'UNOC');
$unb?->syntaxIdentifier();
$unb?->senderIdentification();
$unb?->recipientIdentification();
$unb?->preparationDate();
$unb?->interchangeControlReference();

foreach ($result->functionalGroups() as $group) {
    $group->messageType();
    $group->header()->groupReference();
    $group->trailer()?->controlCount();
    count($group);
    foreach ($group as $message) { }
}
```

## Statistics

```php
use EdifactParser\Analysis\MessageAnalyzer;

$analyzer = new MessageAnalyzer($message);
$analyzer->getType();
$analyzer->segmentCount();
$analyzer->segmentCountByTag('NAD');
$analyzer->lineItemCount();
$analyzer->getPartyQualifiers();
$analyzer->getCurrencies();
$analyzer->calculateTotalAmount('79');
$analyzer->calculateTotalQuantity('21');
$analyzer->hasSegment('UNS');
$analyzer->getSummary();
```

## Segment groups (directory-driven)

`contextSegments()` and `lineItems()` come from `GroupingRules`, a configurable heuristic:
one flat parent/child tag list applied to every message type. The standard instead defines a
distinct, arbitrarily nested structure per message and directory — ORDERS D96A has 54 groups.

With directory data available you can group against the real thing:

```php
use EdifactParser\Directory\GroupInstance;
use EdifactParser\Directory\StructureGrouper;
use EdifactParser\Directory\XmlDirectory;

$structure = XmlDirectory::locate('D96A')?->messageStructure('ORDERS');
$nodes = (new StructureGrouper())->group($message, $structure);
// list<GroupInstance|SegmentInterface> — ungrouped segments and group occurrences, in order

foreach ($nodes as $node) {
    if (!$node instanceof GroupInstance) {
        continue;                      // a segment sitting outside any group
    }

    $node->id();                       // 'SG2'
    $node->occurrence();               // 0, 1, 2 …
    $node->segments();                 // segments directly in this occurrence
    $node->segmentByTag('NAD');
    $node->children();                 // nested GroupInstances
    $node->childrenOfGroup('SG5');
    count($node);
    $node->toArray();
}
```

For the message above this yields:

```
UNH
BGM
DTM
SG1#0 [RFF,DTM]
SG2#0 [NAD]
  SG5#0 [CTA,COM]
SG2#1 [NAD]
SG25#0 [LIN,QTY]
  SG28#0 [PRI]
SG25#1 [LIN,QTY]
UNS
UNT
```

Inspecting a structure directly:

```php
$structure->messageType();     // 'ORDERS'
$structure->groupCount();      // 54
$structure->groups();          // array<string, SegmentGroup>, nested ones included
$group = $structure->group('SG2');
$group?->triggerTag();         // 'NAD' — the segment that opens a repetition
$group?->maxRepeat();
$group?->isRequired();
$group?->parts();              // list<SegmentPosition|SegmentGroup>
```

Segments the structure does not account for are still returned, ungrouped: grouping
imperfectly beats dropping data.

## Comparing two interchanges

```php
use EdifactParser\Diff\Difference;
use EdifactParser\Diff\InterchangeDiff;

$diff = new InterchangeDiff();
$differences = $diff->diff($before, $after);   // list<Difference>
$diff->isIdentical($before, $after);           // bool
$diff->diffMessages($beforeMessage, $afterMessage);

foreach ($differences as $difference) {
    $difference->kind();          // Difference::ADDED | REMOVED | CHANGED
    $difference->messageIndex();
    $difference->tag();
    $difference->subId();
    $difference->before();        // ?SegmentInterface
    $difference->after();         // ?SegmentInterface
    $difference->toArray();
    (string) $difference;         // "~ message 0  QTY:21"
}
```

Segments are aligned by a longest-common-subsequence over tag + subId, not by position, so
inserting one segment reports one addition rather than marking everything after it as
changed.
