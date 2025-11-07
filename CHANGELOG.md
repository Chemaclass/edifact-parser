# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

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

## [Previous Releases]

See [GitHub Releases](https://github.com/Chemaclass/EdifactParser/releases) for earlier versions.
