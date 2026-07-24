# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [6.4.0] - 2026-07-24

#### Added
- Composable segment bundles on `SegmentFactory`: `ENVELOPE_SEGMENTS` (the UN*
  service/control segments) and `BUSINESS_SEGMENTS` (header/party/detail/summary).
  `DEFAULT_SEGMENTS` is now their union, so you can build a lean factory with, for
  example, `withSegments(SegmentFactory::ENVELOPE_SEGMENTS + ['NAD' => ...])`.

## [6.3.0] - 2026-07-24

#### Added
- `SegmentFactory::withAdditionalSegments()` registers custom segments on top of
  the defaults, so you no longer have to spread `DEFAULT_SEGMENTS` yourself. A
  custom class under a default tag overrides that default.
- Six new default segments, typed and registered out of the box (26 -> 32):
  `CTA` (contact), `COM` (communication), `TAX` (duty/tax/fee), `PCD`
  (percentage), `PAT` (payment terms), `TOD` (terms of delivery). Only
  version-stable elements are typed; the factory override remains the path for
  dialect-specific accessors.
- Typed accessors for previously raw-only default segments:
  - `CNTControl`: `controlQualifier()`, `controlValue()`, `measureUnit()`.
  - `CUXCurrencyDetails`: `usageQualifier()`, `currencyCode()`, `rateQualifier()`.
  - `MEADimensions`: `measurementPurpose()`, `measuredAttribute()`, `unitCode()`, `value()`.
  - `PCIPackageId`: `markingInstructionsCode()`, `marksAndLabels()`.
  - `PIAAdditionalProductId`: `productIdFunctionQualifier()`, `itemNumber()`, `itemTypeCode()`.

#### Changed
- `SegmentFactory` now rejects a non-existent segment class with a clean
  `InvalidArgumentException` instead of emitting a PHP warning.
- `EdifactParser::parseFile()` no longer emits a PHP warning when the file exists
  but cannot be read; it throws `InvalidFile` as before.

#### Tests
- The library now has **100% line, method and class test coverage**. Reaching it
  removed a few unreachable defensive branches (single-level context recursion)
  and tightened the segment-factory class check.

## [6.2.1] - 2026-07-23

#### Fixed
- `MessageAnalyzer::getPartyQualifiers()` now returns unique values (matching its
  documented contract and `getCurrencies()`).
- `NADBuilder::withPartyId()` no longer drops a party-id component equal to the
  string `'0'`.
- `TransactionMessage::count()` reports the true segment total for messages built
  directly from the keyed map (previously counted distinct tags).

#### Internal
- Routed segment accessors through the shared `AbstractSegment` helpers, deduplicated
  the `MessageRuleSets` service-segment block and the `SegmentQuery` filters, and
  strengthened previously-untyped closure parameters. No API changes.

## [6.2.0] - 2026-07-22

#### Added
- **More typed segments** (registered by default): `FTX` (free text), `LOC` (place),
  `TDT` (transport details), `IMD` (item description), `PAC` (package), `GID`
  (goods item details), each with domain accessors.
- **Character-set decoding** (`Charset\Charset`): map a UNB syntax identifier
  (UNOA/UNOB → ASCII, UNOC → ISO-8859-1, … UNOY → UTF-8) to an encoding and decode
  data values to UTF-8. `UNBInterchangeHeader::characterEncoding()` exposes the
  interchange encoding. Adds an `ext-mbstring` requirement.
- **Predefined validation rule sets** (`Validation\MessageRuleSets`): ready-to-use
  `MessageRuleSet`s for `ORDERS`, `INVOIC`, `DESADV` and `IFTMIN` (mandatory segments
  + typical order), extensible for partner-specific rules.
- **Interchange assembly** (`Writer\InterchangeBuilder` + `Writer\MessageBuilder`):
  build a full UNB…UNZ interchange programmatically with **auto-computed UNT segment
  counts and UNZ interchange control count**, then `toString()` to a ready-to-send
  EDIFACT string. Completes the write path alongside the fluent segment builders.

## [6.1.0] - 2026-07-22

#### Added
- **Rich envelope metadata accessors**:
  - `UNBInterchangeHeader`: `syntaxIdentifier()`, `syntaxVersionNumber()`,
    `senderIdentification()`, `recipientIdentification()`, `preparationDate()`,
    `preparationTime()`, `interchangeControlReference()`.
  - New typed `UNZInterchangeTrailer` (registered by default) with
    `interchangeControlCount()` / `interchangeControlReference()`.
  - `UNTMessageFooter`: `segmentCount()`, `messageReferenceNumber()`.
  - `BGMBeginningOfMessage`: `documentCode()`, `documentNumber()`, `messageFunction()`.
- **`StreamingParser` honours a leading `UNA`**: custom separators and release char
  declared in the service-string advice are used when splitting (previously the
  default `'`/`?` were assumed).

## [6.0.0] - 2026-07-22

#### Added
- **Duplicate-segment preservation**: `TransactionMessage::query()` and the new
  `TransactionMessage::segments()` now return every segment in original order,
  including repeated segments that share a tag + subId (previously collapsed by the
  keyed index). `count()` now reports the true segment total. (#59)
- **Injectable grouping rules** (`GroupingRules`): customize context parents/children
  and the tags that close a line-item section, passed via a new optional second
  argument to the `EdifactParser` constructor. (#62)
- **Sequence validation**: `MessageRuleSet::inSequence(...$tags)` checks the relative
  order of segments; `MessageValidator` reports a `sequence` violation when broken. (#60)

#### Changed (breaking)
- `EdifactParser::__construct()` takes an optional `?GroupingRules $groupingRules`
  second argument. Existing single-argument calls keep working.
- `TransactionMessage::groupSegmentsByMessage()` now takes a `GroupingRules` as its
  first argument (before the variadic segments).
- `TransactionMessage::__construct()` gained a fourth `array $segments` argument (the
  ordered, duplicate-preserving segment list). Optional; defaults to `[]`.
- `TransactionMessage::count()` now returns the total number of segments, not the
  number of distinct tags.
- `TransactionMessage::query()` now preserves duplicates and original order.

##### Migration
- Building a parser: `new EdifactParser($factory)` is unchanged; pass
  `new EdifactParser($factory, $groupingRules)` only to customize grouping.
- Calling `TransactionMessage::groupSegmentsByMessage(...$segments)` directly:
  pass rules first — `groupSegmentsByMessage(GroupingRules::default(), ...$segments)`.
- If you relied on `count($message)` returning the distinct-tag count, use
  `count($message->allSegments())` instead.

## [5.5.0] - 2026-07-22

#### Added
- **Streaming parser** (`StreamingParser`): parse large interchanges incrementally,
  yielding one `TransactionMessage` at a time in bounded memory (only a single
  message is buffered), instead of building the whole result up front. (#61)
- **Structural validation** (`Validation\MessageValidator` + `MessageRuleSet`):
  check a message against a pluggable rule set (required segments and per-tag
  cardinality) and get a list of `ValidationViolation`s back — never throws;
  empty means conforming. (#60)
- **Functional groups (UNG/UNE)**: typed `UNGFunctionalGroupHeader` /
  `UNEFunctionalGroupTrailer` segments and `ParserResult::functionalGroups()`
  returning `FunctionalGroup` objects (header, trailer, messages). Interchanges
  without groups are unaffected — messages stay available flat via
  `transactionMessages()`. (#59)
- **Typed `MOA` (monetary amount) segment** (`Segments\MOAMonetaryAmount`), now
  registered by default with `amountQualifier()`/`amount()`/`amountAsFloat()`/
  `currencyCode()`. `MessageAnalyzer` uses it (previously `MOA` was an
  `UnknownSegment` read via raw values). (#62)
- **EDIFACT writer/serializer** (`Serializer\EdifactSerializer`): render any
  `iterable<SegmentInterface>` back into an EDIFACT string — the inverse of parsing.
  Pairs with the fluent builders to generate messages. Separators and the release
  char are configurable via `Serializer\UnaSeparators` and can prepend a `UNA` segment.
  Round-trips the sample file byte-for-byte through the low-level parser. (#58)

## [5.4.2] - 2026-07-22

#### Changed
- `subId()` on `CUX`/`PRI`/`QTY`/`RFF` segments now throws `MissingSubId` when the
  `[1][0]` component is absent, matching `UNH`/`UNB`/`CNT`/`DTM`. Previously these
  returned an invalid value on malformed input. Valid messages are unaffected.

#### Internal
- Deduplicated segment `subId()` and composite-component accessors into shared
  `AbstractSegment` helpers (`requiredSubId()`, `component()`).
- Added missing `array`/`list` generics on builders, results and the printer,
  and typed line-item keys as `int|string`; fixed an invalid `@returns` tag.
- Removed redundant restatement docblocks; kept EDIFACT domain documentation.

#### Documentation
- Refreshed README and `AGENTS.md`: corrected the RFF qualifier list, dropped
  stale "New!" markers, fixed `master` → `main` badges, and documented the local
  toolchain (Psalm requires PHP ≤ 8.3; PHP-CS-Fixer needs `PHP_CS_FIXER_IGNORE_ENV`
  on newer PHP).

## [5.4.1] - 2025-12-07

- Add `webmozart/assert` as direct dependency (#53)

## [5.4.0] - 2025-11-08

#### Type-Safe Qualifiers with Constants
- **NADQualifier**: Constants for NAD party qualifiers (BUYER, SUPPLIER, CONSIGNEE, CONSIGNOR, DELIVERY_PARTY, INVOICEE, PAYER, CARRIER, FREIGHT_FORWARDER, MANUFACTURER, ULTIMATE_CONSIGNEE, WAREHOUSE_KEEPER)
- **QTYQualifier**: Constants for QTY quantity qualifiers (DISCRETE, CUMULATIVE, CONSUMER_UNITS, DISPATCHED, ORDERED, ON_HAND, RECEIVED, INVOICED, TO_BE_DELIVERED, FREE_GOODS)
- **PRIQualifier**: Constants for PRI price qualifiers (CALCULATION_NET, CALCULATION_GROSS, INFORMATION_PRICE, GROSS, NET, CATALOGUE, CONTRACT, DISCOUNT, LIST, MINIMUM_ORDER, RECOMMENDED_RETAIL)
- **DTMQualifier**: Constants for DTM date/time qualifiers
- **RFFQualifier**: Constants for RFF reference qualifiers
- Type-safe alternative to magic strings (PHP 8.0 compatible)
- Improved IDE autocomplete and type checking
- Can be used in match expressions and queries

#### Fluent Builder Pattern for Segments
- **NADBuilder**: Build NAD segments programmatically with fluent API
    - `withQualifier()`, `withPartyId()`, `withName()`, `withStreet()`, `withCity()`, `withPostalCode()`, `withCountryCode()`
- **QTYBuilder**: Build QTY segments with fluent API
    - `withQualifier()`, `withQuantity()`, `withMeasureUnit()`
- **PRIBuilder**: Build PRI segments with fluent API
    - `withQualifier()`, `withPrice()`, `withPriceType()`
- All segment classes now have static `builder()` method to create builder instances
- Accepts string values (use qualifier constants for type safety)
- Type-safe segment construction with IDE support

#### Message Analysis Tools
- **MessageAnalyzer**: New class for extracting statistics and insights from EDIFACT messages
    - `getType()`: Get message type (ORDERS, INVOIC, DESADV, etc.)
    - `segmentCount()`: Count total segments in message
    - `segmentCountByTag($tag)`: Count segments by specific tag
    - `lineItemCount()`: Count number of line items
    - `addressCount()`: Count NAD segments
    - `getPartyQualifiers()`: Get unique party qualifiers from NAD segments
    - `getCurrencies()`: Get unique currencies from CUX segments
    - `calculateTotalAmount($qualifier)`: Sum monetary amounts from MOA segments (optionally filtered by qualifier)
    - `calculateTotalQuantity($qualifier)`: Sum quantities from QTY segments (optionally filtered by qualifier)
    - `hasSegment($tag)`: Check if message contains specific segment type
    - `hasSummarySection()`: Check if message has UNS summary section
    - `getSummary()`: Get comprehensive message statistics as array
- Useful for validation, reporting, and business logic

## [5.3.0] - 2025-11-08

### Added

#### Fluent Query API
- **SegmentQuery**: New fluent interface for filtering and transforming segments
  - `withTag($tag)`: Filter by single tag
  - `withTags(array $tags)`: Filter by multiple tags
  - `withSubId($subId)`: Filter by subId
  - `where(callable $predicate)`: Custom filtering
  - `ofType($className)`: Filter by segment class type
  - `limit($limit)`: Limit results
  - `skip($offset)`: Skip results
  - `first()`: Get first matching segment
  - `last()`: Get last matching segment
  - `count()`: Count matching segments
  - `exists()`: Check if any segments match
  - `isEmpty()`: Check if no segments match
  - `map(callable $mapper)`: Transform segments
  - `each(callable $callback)`: Execute callback for each segment
  - `get()`: Get all matching segments
- **HasRetrievableSegments**: Added `query()` method to ParserResult, TransactionMessage, and LineItem
- Enables powerful segment filtering with chainable methods
- Cleaner alternative to manual loops and array_filter

#### Typed Accessor Methods for Segments
- **NADNameAddress**: Added `partyQualifier()`, `partyIdentification()`, `partyId()`, `name()`, `street()`, `city()`, `postalCode()`, `countryCode()` methods for easier access to address data
- **QTYQuantity**: Added `qualifier()`, `quantity()`, `quantityAsFloat()`, `measureUnit()` methods for cleaner quantity handling
- **PRIPrice**: Added `qualifier()`, `price()`, `priceAsFloat()`, `priceType()` methods for better price access
- **LINLineItem**: Added `lineNumber()`, `itemNumberIdentification()`, `itemNumber()`, `itemTypeCode()` methods
- **DTMDateTimePeriod**: Added `qualifier()`, `dateTime()`, `formatQualifier()`, `asDateTime()` methods with support for parsing EDIFACT date formats (102, 203, 204) to `DateTimeImmutable`
- **RFFReference**: Added `qualifier()`, `referenceNumber()` methods
- **UNHMessageHeader**: Added `messageReferenceNumber()`, `messageIdentifier()`, `messageType()`, `messageVersionNumber()`, `messageReleaseNumber()`, `controllingAgency()` methods
- **TransactionMessage**: Added `messageType()` method to quickly retrieve message type (e.g., 'ORDERS', 'INVOIC', 'IFTMIN') from UNH segment

#### Debug Helpers
- **AbstractSegment**: Added `toArray()` and `toJson()` methods to all segments for easy debugging and inspection
  - `toArray()`: Returns array with tag, subId, and rawValues
  - `toJson()`: Returns pretty-printed JSON representation

#### Enhanced Error Messages
- **InvalidFile**: Added `withContext()` static method to provide additional error context
- **InvalidFile**: Added `getErrors()` and `getContext()` methods to retrieve error details
- Error messages now include formatted context information when available

### Changed
- Updated dependency `friendsofphp/php-cs-fixer` from v3.75.0 to v3.89.1 for PHP 8.4 compatibility

### Developer Experience
- Added comprehensive tests for all new typed accessor methods
- Improved type hints and documentation for better IDE support
- All segments now have self-documenting method names instead of magic array indices

## [5.2.0] - 2025-05-30

### Fixed
- Fixed LIN segments with incorrect end detection
- Use BREAK_LINEITEM_TAGS with UNS, CNT, UNT for proper line item boundaries

### Added
- Implemented context for segments (parent-child relationships)
- ContextSegment wrapper for hierarchical segment navigation

## [5.1.0] - 2025-04-30

### Added
- Support returning list from composite sub-ids
- Improved `parsedSubId()` to handle array subIds

### Changed
- Upgraded `sabas/edifact` dependency to 1.2

## [5.0.0] - 2023-02-10

### Added
- Support for UNB InterchangeHeader segment
- Global segments (UNA, UNB, UNZ) are now grouped separately from transaction messages

### Changed
- **Breaking**: ParserResult now separates global segments from transaction messages

## [4.0.0] - 2022-11-10

### Added
- Proper treatment of line items with DetailsSectionBuilder
- CUX (Currency Details) and RFF (Reference) segments
- `TransactionMessage::allSegments()` method

### Changed
- **Breaking**: Segment keys changed to 3-letter codes (e.g., 'NAD' instead of full names)
- Improved handling of unknown segments with UnknownSegment class

## [3.0.0] - 2022-10-30

### Added
- PHP 8.0+ support with strict types
- Code quality tools: php-cs-fixer, psalm, phpstan

### Changed
- **Breaking**: Minimum PHP version is now 8.0
- Improved type safety with psalm and phpstan

## [2.2.0] - 2020-09-20

### Changed
- Renamed `EdifactParser::create()` to `::createWithDefaultSegments()`
- Renamed `segmentByName()` to `segmentsByTag()`

### Added
- `segmentByTagAndSubId()` method for direct segment lookup
- PrinterInterface and ConsolePrinter for output formatting
- Split unit and functional test suites

## [2.1.0] - 2020-06-07

### Changed
- Renamed `SegmentInterface::name()` to `tag()`
- Renamed `SegmentInterface::subSegmentKey()` to `subId()`
- Improved SegmentFactory implementation

### Added
- Documentation about EDIFACT files and parser architecture

## [2.0.0] - 2020-06-01

### Changed
- **Breaking**: Minimum PHP version is now 7.4
- **Breaking**: `EdifactParser::parse()` returns `list<TransactionMessage>`
- TransactionMessage acts as DTO with grouped segments
- Improved naming: SegmentedValues → SegmentList, improved method names

### Added
- Psalm level 1 with `@psalm-immutable` and `@psalm-pure` annotations
- Makefile for development tasks

## Earlier Releases

See [GitHub Releases](https://github.com/Chemaclass/EdifactParser/releases) for versions prior to 2.0.0.
