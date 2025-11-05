# AGENTS.md

This file provides guidance to AI coding assistants when working with code in this repository.

## Project Overview

PHP library for parsing EDIFACT (Electronic Data Interchange) files. Reads, interprets, and extracts data from EDIFACT-formatted messages and segments.

## Commands

```bash
# Setup
composer install

# Testing
composer test                    # All tests
composer test-unit              # Unit tests only
composer test-functional        # Functional tests only
./vendor/bin/phpunit --filter testMethodName   # Specific test

# Code Quality
composer quality                # All checks (CS, Psalm, PHPStan, Rector)
composer csfix                  # Fix code style
composer psalm                  # Static analysis (Psalm)
composer phpstan                # Static analysis (PHPStan)
composer rector                 # Apply refactoring rules
```

## Architecture

### Parsing Flow

1. **EdifactParser** → Entry point, uses `sabas/edifact` library, returns `ParserResult`
2. **SegmentFactory** → Maps 3-char tags (UNH, NAD, LIN) to segment classes
3. **ParserResult** → Contains `globalSegments` (file-level) and `transactionMessages` (UNH...UNT blocks)
4. **TransactionMessage** → Single message with `groupedSegments`, `lineItems`, `contextSegments`

### Segment Organization

Segments organized three ways in TransactionMessage:

- **groupedSegments**: Flat lookup by tag/subId `['NAD']['CN']`
- **lineItems**: LIN segments with related children (product/item details)
- **contextSegments**: Hierarchical parent-child relationships (NAD→CTA→COM)

Context rules in `ContextStackParser`:
- Parents: NAD, LIN, DOC
- Children: COM, CTA, PIA, IMD, MEA, QTY, PRI, TAX, DTM, MOA

### Key Classes

- **SegmentInterface** - `tag()`, `subId()`, `rawValues()`, `parsedSubId()`
- **AbstractSegment** - Base for custom segments
- **ContextSegment** - Wraps segments with `children()`
- **HasRetrievableSegments** - Trait for `segmentsByTag()` and `segmentByTagAndSubId()`

### Extending

Add custom segment: Implement `SegmentInterface`, pass to `SegmentFactory::withSegments(['TAG' => Class::class])`

## Code Standards

- PHP 8.0+, strict types, PSR-4
- Must pass: PHP-CS-Fixer, Psalm, PHPStan, Rector
- Type hints required
- Tests required (Unit or Functional)
