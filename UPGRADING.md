# Upgrading

## 6.x → 7.0

Three behaviours changed. Two of them are bug fixes that were impossible to make
compatibly — the old behaviour destroyed data or made documented code impossible — and one
is a return-type change with a one-line replacement.

Most applications need no changes at all. Work through the three checks below.

---

### 1. Keyed lookups return the typed segment, not a `ContextSegment`

**What changed.** `NAD`, `LIN` and `DOC` open a context by default, and the context object
used to replace the segment in the keyed views. `ContextSegment` proxies `tag()`, `subId()`
and `rawValues()` but none of the typed accessors — so this was a fatal error:

```php
$buyer = $message->segmentByTagAndSubId('NAD', 'BY');
$buyer->name();   // 6.x: Call to undefined method EdifactParser\ContextSegment::name()
```

That is the README Quick Start, and it could not work. It now does.

**Affects** `allSegments()`, `segmentsByTag()`, `segmentByTagAndSubId()` on both
`TransactionMessage` and `LineItem`.

**You need to change something only if** you called `children()` on the result of a keyed
lookup — which required knowing the accessors did *not* work, so this is rare.

```php
// 6.x
$message->segmentByTagAndSubId('NAD', 'BY')->children();

// 7.0
$buyer = $message->segmentByTagAndSubId('NAD', 'BY');
$message->childrenOf($buyer);       // list<SegmentInterface>
$message->contextFor($buyer);       // ?ContextSegment, if you want the context object
```

`contextSegments()` is unchanged, and still returns `ContextSegment` objects.

**What gets better:** typed accessors and `instanceof NADNameAddress` now work on keyed
lookups, as the documentation always claimed.

---

### 2. Non-ASCII data is preserved

**What changed.** The default tokenizer is now `NativeTokenizer`. The 6.x default,
`sabas/edifact`, strips every byte in `\x80-\xFF` regardless of the interchange's declared
syntax identifier:

```php
$edi = "UNH+1+ORDERS:D:96A:UN'NAD+BY+++Müller GmbH'UNT+3+1'";
$message->segmentByTagAndSubId('NAD', 'BY')->name();
// 6.x: "Mller GmbH"
// 7.0: "Müller GmbH"
```

`UNOC` is Latin-1 and `UNOY` is UTF-8 — between them, most European traffic. All of it was
unusable outside 7-bit ASCII.

**You need to change something only if** you depend on the stripping, for example because a
downstream system cannot accept non-ASCII and you were relying on the parser to remove it.
In that case do it explicitly rather than by accident, or keep the old tokenizer:

```php
use EdifactParser\Tokenizer\SabasTokenizer;

new EdifactParser(SegmentFactory::withDefaultSegments(), tokenizer: new SabasTokenizer());
EdifactParser::createWithDefaultSegments(tokenizer: new SabasTokenizer());
```

For ASCII input the two tokenize identically — verified segment-for-segment across the test
fixtures and a generated corpus.

---

### 3. Malformed input raises `InvalidFile` instead of being accepted

**What changed.** `EdifactParser::parse()` read `errors()` before the underlying parser had
done the work that raises them, so almost nothing was ever reported. Input that was silently
accepted and returned mangled now throws.

**You need to change something only if** you were feeding the parser malformed interchanges
and processing whatever came back. Those will now surface as exceptions — which is the
point, but it may be a new failure mode in your pipeline.

```php
use EdifactParser\Exception\InvalidFile;

try {
    $result = $parser->parseFile($path);
} catch (InvalidFile $e) {
    foreach ($e->getDiagnostics() as $diagnostic) {
        $diagnostic->code();          // 'segment.unterminated' — stable, match on this
        $diagnostic->segmentIndex();  // where it stopped
        $diagnostic->tag();
    }
}
```

`getErrors()` still returns the plain strings, so existing handling keeps working.

---

## Worth adopting, but optional

Nothing below is required by the upgrade.

| | |
| --- | --- |
| `edifact` CLI | `edifact parse\|inspect\|validate\|segments` — JSON on stdout, exit 0/1/2 |
| Directory validation | `DirectoryValidator` checks elements, lengths, representations and code lists against UNTDID data |
| Segment groups | `StructureGrouper` groups against the directory's real nested `SG1…SGn`, instead of the `GroupingRules` heuristic |
| More typed segments | `SegmentFactory::withDirectorySegments()` — 134 tags instead of 32 |
| Diagnostics | Stable `DiagnosticCode` values across parsing and validation |
| Introspection | `registeredTags()`, `classForTag()`, `describeTag()` |
| Syntax 4 | The `UNA` repetition separator is read and honoured |

`GroupingRules`, `contextSegments()` and `lineItems()` are unchanged and remain the default
grouping path. Adopting directory-driven grouping is opt-in.

See [CHANGELOG.md](CHANGELOG.md) for the full list, and
[docs/llms/](docs/llms) for per-topic documentation.
