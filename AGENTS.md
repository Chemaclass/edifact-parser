# AGENTS.md

AI context for understanding this EDIFACT parser library architecture.

## Core Architecture

**Parsing flow:** EdifactParser → SegmentFactory → ParserResult

1. **EdifactParser** (`src/EdifactParser.php`)
   - Entry point, uses `sabas/edifact` for low-level parsing
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
- ContextSegment: Decorator with `children()` for hierarchy
- HasRetrievableSegments: Trait for `segmentsByTag()`, `segmentByTagAndSubId()`, `query()`

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
- **`SegmentQuery`** (`$message->query()`): fluent `withTag/withTags/withSubId/where/ofType/
  limit/skip/first/last/get/count/exists/isEmpty/map/each`
- **`Analysis\MessageAnalyzer`**: counts, `getPartyQualifiers()`, `getCurrencies()`,
  `calculateTotalAmount()/Quantity()`, `getSummary()`
- **Fluent builders** (`Segments\Builder\*`): `NADNameAddress::builder()` etc. → `build()`
- **Qualifier constants** (`Segments\Qualifier\*`): NAD/QTY/PRI/DTM/RFF magic-string maps
- **Writer** (`Serializer\EdifactSerializer` + `UnaSeparators`): render `iterable<SegmentInterface>`
  back to an `.edi` string (inverse of parsing)
- **Validation** (`Validation\MessageValidator` + `MessageRuleSet` → `ValidationViolation`):
  required-segment, cardinality and `inSequence()` conformance checks; never throws
- **Duplicate-preserving access**: `query()` and `TransactionMessage::segments()` keep
  every segment in order (dups included); keyed views index by tag+subId (last wins)
- **Grouping config** (`GroupingRules`): injectable context/child/line-item-break tags
- **Streaming** (`StreamingParser`): generator yielding one `TransactionMessage` at a time,
  bounded memory for large interchanges; honours a leading `UNA` (custom delimiters)
- **Functional groups** (`ParserResult::functionalGroups()` → `FunctionalGroup`): UNG/UNE
  envelope; messages also stay available flat via `transactionMessages()`

## Extension Points

- Add custom segments: Extend AbstractSegment, register in SegmentFactory
- Modify context / line-item rules: pass a customized `GroupingRules` to the
  `EdifactParser` constructor (no longer hardcoded consts)
- Custom builders: Implement BuilderInterface for different grouping logic

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

**Toolchain gotcha:** the pinned Psalm (`^4.30`) only runs on **PHP ≤ 8.3** — run it under
8.3 if your CLI is newer. On PHP > 8.3, PHP-CS-Fixer needs `PHP_CS_FIXER_IGNORE_ENV=1`.
PHPStan passing does not guarantee Psalm passes (Psalm is stricter about union returns
from `rawValues()` accessors) — run both before pushing.
