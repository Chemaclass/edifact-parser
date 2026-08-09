# AGENTS.md

AI context for understanding this EDIFACT parser library architecture.

## Core Architecture

**Parsing flow:** EdifactParser → SegmentFactory → ParserResult

1. **EdifactParser** (`src/EdifactParser.php`)
   - Entry point; tokenizing is pluggable via `Tokenizer\TokenizerInterface`
     - `NativeTokenizer` (default) — regex-free single pass, ~2.2x faster, and lossless
     - `SabasTokenizer` — wraps `sabas/edifact`, the 6.x default. Strips every byte in
       \x80-\xFF, so it destroys non-ASCII data. Kept for bug-for-bug compatibility.
     - Kept honest by `TokenizerEquivalenceTest`, which diffs `NativeTokenizer` against
       `EDI\Parser` over fixtures plus a generated corpus; extend it before touching either.
       It compares raw tokenization, not error policy — some fixtures are deliberately
       malformed and `SabasTokenizer` rejects them.
   - Delegates to SegmentFactory for typed segment objects
   - Returns ParserResult

2. **SegmentFactory** (`src/Segments/SegmentFactory.php`)
   - Maps 3-char tags (UNH, NAD, LIN) to segment classes
   - All segments implement SegmentInterface
   - Returns UnknownSegment for unregistered tags

3. **ParserResult** (`src/ParserResult.php`)
   - Contains `globalSegments` (UNA, UNB, UNZ - file-level)
   - Contains `transactionMessages[]` (UNH...UNT message blocks)

4. **TransactionMessage** (`src/TransactionMessage.php`)
   - Single UNH...UNT message block
   - Contains `groupedSegments`, `lineItems[]`, `contextSegments[]`

## Segment Organization

TransactionMessage organizes segments three ways:

1. **groupedSegments**: Flat lookup `['NAD']['CN']` - fastest access
2. **lineItems**: LIN segments with children (products/orders) - created by DetailsSectionBuilder
3. **contextSegments**: Hierarchical parent-child - built by ContextStackParser

## Key Patterns

**Context hierarchy** (`ContextStackParser`, defaults — override via `GroupingRules`):
- Parents: NAD, LIN, DOC
- Children: COM, CTA, PIA, IMD, MEA, QTY, PRI, TAX, DTM, MOA

**Line item boundaries** (`MessageDataBuilder\Builder`, defaults — override via `GroupingRules`):
- Start: LIN segment
- End: UNS, CNT, or UNT segments
- DetailsSectionBuilder groups segments into line items
- SimpleBuilder handles flat grouping

**Segment abstraction**:
- SegmentInterface: `tag()`, `subId()`, `rawValues()`, `parsedSubId()`
- AbstractSegment: Base implementation. Shared protected helpers segments delegate to:
  - `requiredSubId()` — subId from `rawValues[1][0]`, throws `MissingSubId` if absent
  - `component(int $index, int $group = 1)` — read a composite element, `''` if absent
- ContextSegment: Decorator with `children()` for hierarchy; also `childByTag()`,
  `childrenByTag()`, `hasChildren()`, `toArray()/toJson()`, Countable + IteratorAggregate
- HasRetrievableSegments: Trait for `segmentsByTag()`, `segmentByTagAndSubId()`, `query()`
- SegmentArray: `fromSegment()`/`fromSegments()` — the one place segments become plain
  arrays; every `toArray()` in the library goes through it

**Keyed maps use `array-key`, not `string`**: PHP normalizes a numeric-looking subId
('1', '21') to an int array key, so grouped maps are typed
`array<string, array<array-key, SegmentInterface>>` and `segmentByTagAndSubId()` takes
`string|int`.

**SubId logic**:
- Base `subId()` reads `rawValues()[1]`; string `'CN'` or array `['21', 'C62']` → joined as `'21:C62'`
- Segments with a mandatory composite id (UNH/UNB/CNT/DTM/CUX/PRI/QTY/RFF) override
  `subId()` with `requiredSubId()` — these throw `MissingSubId` on malformed input
- Used for distinguishing multiple segments with the same tag

## Public API Surface (for extraction/consumption)

- **Typed accessors** on segments: e.g. `NADNameAddress::name()/countryCode()`,
  `QTYQuantity::quantityAsFloat()`, `PRIPrice::priceAsFloat()`, `DTMDateTimePeriod::asDateTime()`
- **Envelope metadata**: `UNBInterchangeHeader` (syntax id/version, sender/recipient,
  prep date/time, control ref), `UNZInterchangeTrailer`, `UNTMessageFooter`, `BGMBeginningOfMessage`
- **`SegmentQuery`** (`$message->query()`): fluent `withTag/withTags/withoutTags/withSubId/
  where/ofType/limit/skip/first/last/get/count/exists/isEmpty/map/reduce/groupByTag/
  countByTag/each`; Countable + IteratorAggregate
- **Collections**: `ParserResult` (messages), `TransactionMessage` (segments, in order),
  `LineItem` (segments), `FunctionalGroup` (messages) are Countable + IteratorAggregate.
  `ParserResult::firstMessage()/messagesOfType()`; `TransactionMessage::has()/countByTag()/
  toArray()/toJson()`
- **Bulk entry points that skip argument unpacking**: `TransactionMessage::groupSegments()`
  and `ContextStackParser::parseAll()` take an `iterable`; the variadic
  `groupSegmentsByMessage()`/`parse()` delegate to them
- **`Analysis\MessageAnalyzer`**: counts, `getPartyQualifiers()`, `getCurrencies()`,
  `calculateTotalAmount()/Quantity()`, `getSummary()`
- **Fluent builders** (`Segments\Builder\*`): `NADNameAddress::builder()` etc. → `build()`
- **Qualifier constants** (`Segments\Qualifier\*`): NAD/QTY/PRI/DTM/RFF magic-string maps
- **Writer** (`Serializer\EdifactSerializer` + `UnaSeparators`): render `iterable<SegmentInterface>`
  back to an `.edi` string (inverse of parsing)
- **Interchange assembly** (`Writer\InterchangeBuilder` + `Writer\MessageBuilder`): build a full
  UNB…UNZ with auto UNT/UNZ counts, then `toString()`
- **Predefined rule sets** (`Validation\MessageRuleSets`): `orders()`/`invoic()`/`desadv()`/`iftmin()`
- **Charset** (`Charset\Charset`): map UNB syntax id → encoding, decode values to UTF-8
- **Diagnostics** (`Diagnostics\Diagnostic` + `DiagnosticCode`): one type for parse and
  validation failures — stable code, severity, segment index, tag, element path,
  `toArray()`/`toJson()`. Reached via `InvalidFile::getDiagnostics()`,
  `MessageValidator::diagnose()` and `ValidationViolation::toDiagnostic()`. **Codes are
  public API and must stay stable; messages are free to change** — never match on message text
- **Validation** (`Validation\MessageValidator` + `MessageRuleSet` → `ValidationViolation`):
  required-segment, cardinality and `inSequence()` conformance checks; never throws
- **Duplicate-preserving access**: `query()` and `TransactionMessage::segments()` keep
  every segment in order (dups included); keyed views index by tag+subId (last wins)
- **Keyed views hold the typed segment, never a `ContextSegment`** — so
  `segmentByTagAndSubId('NAD', 'BY')->name()` and `instanceof NADNameAddress` both work.
  Go from a segment to what was grouped under it with
  `TransactionMessage::childrenOf()`/`contextFor()` (indexed by `spl_object_id`, and
  accepting either the segment or the context object)
- **Introspection** (`SegmentFactory::registeredTags()/classForTag()/describeTag()`,
  `Segments\SegmentDescriptor`): enumerate tags and accessors at runtime. `registeredTags()`
  and `classForTag()` must never autoload segment classes — see Hot Paths. Descriptors are
  reflection-derived; do not hand-maintain them. `schema/message.schema.json` publishes the
  `toArray()` shape and is asserted against real output by a test
- **Grouping config** (`GroupingRules`): injectable context/child/line-item-break tags
- **Streaming** (`StreamingParser`): generator yielding one `TransactionMessage` at a time,
  bounded memory for large interchanges; honours a leading `UNA` (custom delimiters)
- **Functional groups** (`ParserResult::functionalGroups()` → `FunctionalGroup`): UNG/UNE
  envelope; messages also stay available flat via `transactionMessages()`

## Extension Points

- Add custom segments: Extend AbstractSegment, register in SegmentFactory
- Modify context / line-item rules: pass a customized `GroupingRules` to the
  `EdifactParser` constructor or to `createWithDefaultSegments()` (no longer hardcoded consts)
- Custom builders: Implement BuilderInterface for different grouping logic

## Hot Paths (do not regress)

Enforced, not just documented: `composer bench` measures each of these, and CI runs the
same suite against the PR's base branch on the same runner, failing on a >1.5x regression.
Do not edit `tools/benchmark.php` in a commit that also claims a performance delta.

These run once per segment of an interchange — hundreds of thousands of times on a large
file. Keep them allocation- and call-free:

- `SegmentFactory::createSegmentFromArray()` — no per-instance validation; classes are
  checked once on construction, and `withDefaultSegments()` skips even that (guarded by
  `SegmentFactoryTest::every_default_class_implements_the_segment_interface`) so building a
  factory does not autoload all 32 segment classes.
- `GroupingRules::is*Tag()` — hash lookups over maps built in the constructor, not `in_array`.
- `MessageDataBuilder\Builder::addSegment()` — state transitions inlined on purpose.
- `TransactionMessage::groupSegments()` — one pass; global (UNA/UNB/UNZ) segments are
  collected inside it rather than by a second filter pass.
- `StreamingParser::extractSegments()` — `strcspn`/`substr` runs, never a per-character loop.
- `TransactionMessage` memoizes its ordered segment list and tag counts; `ParserResult`
  memoizes the merged segment map.

## Conventions & Constraints

- **Min PHP 8.0** (`composer.json` `platform.php: 8.0`) — enums (8.1) are NOT available;
  use `final class` + `public const` for constant groups (see `Segments/Qualifier/*`).
- Public library: preserve method signatures and const values; changes to them are BC breaks.
- All code passes PHP-CS-Fixer, Psalm, PHPStan (level 5) and Rector; tests required for new behavior.
- Conventional commits (`ref:` for refactors); land work via a branch + PR.

## Commands

```bash
composer test-unit              # Unit tests
composer test-functional        # Functional tests
composer quality                # All checks (CS, Psalm, PHPStan, Rector)
composer csfix                  # Fix code style
```

**Toolchain gotcha:** the pinned Psalm (`^5.26`) is happiest on **PHP ≤ 8.3** — run it under
8.3 if your CLI is newer, and add `--threads=1` if it dies mid-run. On PHP > 8.3,
PHP-CS-Fixer needs `PHP_CS_FIXER_IGNORE_ENV=1`. PHPStan passing does not guarantee Psalm
passes (Psalm is stricter about union returns from `rawValues()` accessors) — run both
before pushing. CI enforces **100% line coverage**, so every new method needs a test.
