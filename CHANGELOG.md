# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
