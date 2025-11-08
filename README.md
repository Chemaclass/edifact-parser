# 📦 EDIFACT Parser

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/?branch=master)
[![Type Coverage](https://shepherd.dev/github/Chemaclass/EdifactParser/coverage.svg)](https://shepherd.dev/github/chemaclass/EdifactParser)
[![CI](https://github.com/Chemaclass/EdifactParser/workflows/CI/badge.svg?branch=master)](https://github.com/Chemaclass/EdifactParser/actions)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)

**EDIFACT** stands for _Electronic Data Interchange For Administration, Commerce, and Transport_.

This package provides a robust and extensible **PHP parser** to read, interpret, and extract data from EDIFACT-formatted files.

🔍 Not sure what EDIFACT is? [Learn more here](/docu/README.md)

---

## 📚 EDIFACT Format Overview

- A file is composed of multiple **segments**—each begins with a **tag** (e.g., `UNH`, `NAD`).
- Each segment contains structured data relevant to that tag.
- A **message** typically starts with a `UNH` segment and ends with a `UNT` segment.
- A **transaction** is a list of such messages within a file.

👉 Read more about segments [here](/docu/segments/README.md)

---

## 💾 Installation

Install via [Composer](https://packagist.org/packages/chemaclass/edifact-parser):

```bash
composer require chemaclass/edifact-parser
```

## 🧪 Examples

### 🔎 Usage example

```php
<?php declare(strict_types=1);

use EdifactParser\EdifactParser;

require dirname(__DIR__) . '/vendor/autoload.php';

$fileContent = <<<EDI
...
NAD+CN+++Person Name+Street Nr 2+City2++12345+DE'
...
EDI;

$parser = EdifactParser::createWithDefaultSegments();
$parserResult = $parser->parse($fileContent);
// Or directly from a file
//$parserResult = $parser->parseFile('/path/to/file.edi');
$firstMessage = $parserResult->transactionMessages()[0];

$nadSegment = $firstMessage->segmentByTagAndSubId('NAD', 'CN');
$personName = $nadSegment->rawValues()[4]; // 'Person Name'
```

### 📂 More Examples

- [example/extracting-data.php](example/extracting-data.php) — Extract values using typed accessors and query API.
- [example/query-filtering.php](example/query-filtering.php) — Advanced filtering with fluent query API.
- [example/printing-segments.php](example/printing-segments.php) — Print all parsed segments with statistics.
- [example/context-segments.php](example/context-segments.php) — Traverse hierarchical context segments.

---

## 📖 Usage Guide

### Typed Accessor Methods (New! ✨)

Many segments now provide typed accessor methods for cleaner, self-documenting code:

```php
// NAD (Name and Address) - No more magic array indices!
$nadSegment = $message->segmentByTagAndSubId('NAD', 'CN');
$name = $nadSegment->name();              // Instead of rawValues()[4]
$street = $nadSegment->street();          // Instead of rawValues()[5]
$city = $nadSegment->city();              // Instead of rawValues()[6]
$postalCode = $nadSegment->postalCode();  // Instead of rawValues()[8]
$country = $nadSegment->countryCode();    // Instead of rawValues()[9]

// QTY (Quantity) - With type conversion
$qtySegment = $lineItem->segmentByTagAndSubId('QTY', '21');
$quantity = $qtySegment->quantityAsFloat();  // Returns float
$unit = $qtySegment->measureUnit();          // e.g., 'PCE', 'KGM'

// PRI (Price) - With type conversion
$priSegment = $lineItem->segmentByTagAndSubId('PRI', 'AAA');
$price = $priSegment->priceAsFloat();  // Returns float

// DTM (Date/Time) - With date parsing
$dtmSegment = $message->segmentByTagAndSubId('DTM', '10');
$dateTime = $dtmSegment->asDateTime();  // Returns DateTimeImmutable or null

// Message type detection
$messageType = $message->messageType();  // Returns 'ORDERS', 'INVOIC', etc.
```

### Accessing Segments

```php
// Direct lookup by tag and subId (fastest)
$nadSegment = $message->segmentByTagAndSubId('NAD', 'BY');

// Get all segments with the same tag
$allNadSegments = $message->segmentsByTag('NAD');

// Always null-check when accessing segments
if ($nadSegment) {
    // Use typed accessors for cleaner code
    $companyName = $nadSegment->name();
    // Or access raw values directly if needed
    $companyName = $nadSegment->rawValues()[4];
}
```

### Fluent Query API (New! ✨)

Chain filters and transformations for powerful segment querying:

```php
// Find all NAD segments with subId 'CN'
$consignees = $message->query()
    ->withTag('NAD')
    ->withSubId('CN')
    ->get();

// Find first supplier address
$supplier = $message->query()
    ->withTag('NAD')
    ->withSubId('SU')
    ->first();

// Get all NAD and LIN segments
$segments = $message->query()
    ->withTags(['NAD', 'LIN'])
    ->get();

// Filter by type
$addresses = $message->query()
    ->ofType(NADNameAddress::class)
    ->get();

// Custom filtering with predicates
$highValueItems = $message->query()
    ->withTag('PRI')
    ->where(fn($s) => $s->priceAsFloat() > 1000)
    ->get();

// Chain multiple filters
$germanSuppliers = $message->query()
    ->withTag('NAD')
    ->withSubId('SU')
    ->where(fn($s) => $s->countryCode() === 'DE')
    ->limit(10)
    ->get();

// Transform results
$companyNames = $message->query()
    ->withTag('NAD')
    ->map(fn($s) => $s->name());

// Check existence
if ($message->query()->withTag('UNS')->exists()) {
    // Process summary section...
}

// Count matching segments
$nadCount = $message->query()->withTag('NAD')->count();
```

### Type-Safe Qualifiers with Constants (New! ✨)

Use predefined constants for common EDIFACT qualifiers to avoid magic strings and improve IDE autocomplete:

```php
use EdifactParser\Segments\Qualifier\NADQualifier;
use EdifactParser\Segments\Qualifier\QTYQualifier;
use EdifactParser\Segments\Qualifier\PRIQualifier;

// NAD qualifiers - party roles
$buyer = NADQualifier::BUYER;           // 'BY'
$supplier = NADQualifier::SUPPLIER;     // 'SU'
$consignee = NADQualifier::CONSIGNEE;   // 'CN'
$carrier = NADQualifier::CARRIER;       // 'CA'

// QTY qualifiers - quantity types
$ordered = QTYQualifier::ORDERED;       // '21'
$dispatched = QTYQualifier::DISPATCHED; // '12'
$invoiced = QTYQualifier::INVOICED;     // '47'

// PRI qualifiers - price types
$netPrice = PRIQualifier::CALCULATION_NET;  // 'AAA'
$grossPrice = PRIQualifier::GROSS;          // 'AAF'
$listPrice = PRIQualifier::LIST;            // 'LIS'

// Use in queries
$buyers = $message->query()
    ->withTag('NAD')
    ->where(fn($s) => $s->partyQualifier() === NADQualifier::BUYER)
    ->get();

// Use in match expressions
$role = match ($segment->partyQualifier()) {
    NADQualifier::BUYER => 'Customer',
    NADQualifier::SUPPLIER => 'Vendor',
    default => 'Unknown'
};
```

Available qualifier constants:
- `NADQualifier` - Party roles (BY, SU, CN, CZ, DP, IV, PR, CA, FW, MF, UC, WH)
- `QTYQualifier` - Quantity types (1, 3, 11, 12, 21, 33, 46, 47, 48, 192)
- `PRIQualifier` - Price types (AAA, AAB, AAE, AAF, AAG, CAL, CT, DIS, LIS, MIN, RRP)
- `DTMQualifier` - Date/time types (137, 2, 10, 11, etc.)
- `RFFQualifier` - Reference types (ON, CR, DQ, PD, etc.)

### Building Segments with Fluent Builders (New! ✨)

Create segment objects programmatically with a fluent, type-safe API:

```php
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Segments\PRIPrice;
use EdifactParser\Segments\Qualifier\NADQualifier;
use EdifactParser\Segments\Qualifier\QTYQualifier;
use EdifactParser\Segments\Qualifier\PRIQualifier;

// Build NAD segment
$nadSegment = NADNameAddress::builder()
    ->withQualifier(NADQualifier::BUYER)
    ->withPartyId('123456')
    ->withName('ACME Corporation')
    ->withStreet('123 Main Street')
    ->withCity('Springfield')
    ->withPostalCode('12345')
    ->withCountryCode('US')
    ->build();

// Build QTY segment
$qtySegment = QTYQuantity::builder()
    ->withQualifier(QTYQualifier::ORDERED)
    ->withQuantity(100)
    ->withMeasureUnit('PCE')
    ->build();

// Build PRI segment
$priSegment = PRIPrice::builder()
    ->withQualifier(PRIQualifier::CALCULATION_NET)
    ->withPrice(99.99)
    ->withPriceType('CT')
    ->build();

// Use built segments
echo $nadSegment->name();           // 'ACME Corporation'
echo $qtySegment->quantityAsFloat(); // 100.0
echo $priSegment->priceAsFloat();    // 99.99
```

### Message Statistics and Analysis (New! ✨)

Analyze EDIFACT messages to extract statistics and insights:

```php
use EdifactParser\Analysis\MessageAnalyzer;

$analyzer = new MessageAnalyzer($message);

// Basic counts
$type = $analyzer->getType();              // 'ORDERS', 'INVOIC', etc.
$totalSegments = $analyzer->segmentCount(); // Total number of segments
$lineItems = $analyzer->lineItemCount();   // Number of line items
$addresses = $analyzer->addressCount();    // Number of NAD segments

// Segment-specific counts
$qtyCount = $analyzer->segmentCountByTag('QTY');
$priCount = $analyzer->segmentCountByTag('PRI');

// Extract unique values
$partyQualifiers = $analyzer->getPartyQualifiers();  // ['BY', 'SU', 'CN']
$currencies = $analyzer->getCurrencies();            // ['EUR', 'USD']

// Calculate totals
$totalAmount = $analyzer->calculateTotalAmount();      // Sum all MOA segments
$taxableAmount = $analyzer->calculateTotalAmount('125'); // Sum MOA with qualifier 125
$totalQty = $analyzer->calculateTotalQuantity();       // Sum all QTY segments
$orderedQty = $analyzer->calculateTotalQuantity('21'); // Sum ordered quantities

// Check for specific segments
if ($analyzer->hasSegment('UNS')) {
    // Message has summary section
}
if ($analyzer->hasSummarySection()) {
    // Shortcut for UNS check
}

// Get comprehensive summary
$summary = $analyzer->getSummary();
/*
[
    'message_type' => 'ORDERS',
    'total_segments' => 42,
    'line_items' => 5,
    'addresses' => 3,
    'party_qualifiers' => ['BY', 'SU', 'CN'],
    'currencies' => ['EUR'],
    'segment_counts' => [
        'NAD' => 3,
        'LIN' => 5,
        'QTY' => 5,
        'PRI' => 5,
        'MOA' => 2,
        'DTM' => 4,
    ],
]
*/
```

### Working with Line Items

Line items group LIN segments with their related data (QTY, PRI, PIA, etc.) — useful for processing orders and invoices:

```php
foreach ($message->lineItems() as $lineItem) {
    $linSegment = $lineItem->segmentByTagAndSubId('LIN', '1');
    $qtySegment = $lineItem->segmentByTagAndSubId('QTY', '21');

    $productId = $linSegment->rawValues()[3];
    $quantity = $qtySegment->rawValues()[1][0];
}
```

### Navigating Hierarchical Segments

Context segments maintain parent-child relationships (e.g., NAD → CTA → COM):

```php
foreach ($message->contextSegments() as $context) {
    if ($context->tag() === 'NAD') {
        $address = $context->segment()->rawValues();

        foreach ($context->children() as $child) {
            if ($child->tag() === 'CTA') {
                $contactName = $child->rawValues()[2];
            }
        }
    }
}
```

### Global vs Transaction Segments

```php
// Global segments (file-level): UNA, UNB, UNZ
$globalSegments = $result->globalSegments();

// Transaction messages (UNH...UNT blocks)
foreach ($result->transactionMessages() as $message) {
    // Process each message...
}
```

---

## 🔧 Extending with Custom Segments

### Step 1: Create Your Segment Class

```php
<?php
namespace YourApp\Segments;

use EdifactParser\Segments\AbstractSegment;

/** @psalm-immutable */
final class LOCLocation extends AbstractSegment
{
    public function tag(): string
    {
        return 'LOC';
    }

    // Optional: Add helper methods
    public function locationType(): string
    {
        return $this->rawValues()[1] ?? '';
    }

    public function locationCode(): string
    {
        return $this->rawValues()[2][0] ?? '';
    }
}
```

### Step 2: Register with SegmentFactory

```php
use EdifactParser\Segments\SegmentFactory;
use YourApp\Segments\LOCLocation;

$factory = SegmentFactory::withSegments([
    ...SegmentFactory::DEFAULT_SEGMENTS,  // Keep defaults
    'LOC' => LOCLocation::class,          // Add yours
]);

$parser = new EdifactParser($factory);
```

### Step 3: Write Tests

```php
use PHPUnit\Framework\TestCase;
use YourApp\Segments\LOCLocation;

final class LOCLocationTest extends TestCase
{
    /** @test */
    public function it_parses_location_data(): void
    {
        $raw = ['LOC', '11', ['DEHAM', '139', '6'], 'Hamburg'];
        $segment = new LOCLocation($raw);

        self::assertEquals('LOC', $segment->tag());
        self::assertEquals('11', $segment->subId());
        self::assertEquals('DEHAM', $segment->locationCode());
    }
}
```

---

## 🐛 Debugging

### Segment Inspection

```php
// Convert segment to array
$array = $segment->toArray();
// Returns: ['tag' => 'NAD', 'subId' => 'CN', 'rawValues' => [...]]

// Convert segment to JSON
$json = $segment->toJson();
// Pretty-printed JSON output

// Detect message type
$type = $message->messageType();
echo "Processing {$type} message";  // e.g., "Processing ORDERS message"
```

### Enhanced Error Messages

```php
use EdifactParser\Exception\InvalidFile;

try {
    $result = $parser->parseFile('invalid.edi');
} catch (InvalidFile $e) {
    // Get detailed error information
    $errors = $e->getErrors();
    $context = $e->getContext();

    // Exception message includes formatted context
    echo $e->getMessage();
}
```

---

## ✅ Best Practices

### Do

- ✅ Use typed accessors (e.g., `$nad->name()`) instead of raw array indices
- ✅ Always null-check segments — not all segments exist in every message
- ✅ Use `segmentByTagAndSubId()` for single lookups, `segmentsByTag()` for multiple
- ✅ Use type conversion methods (`quantityAsFloat()`, `asDateTime()`) when available
- ✅ Use line items for order/invoice processing — cleaner than manual grouping
- ✅ Add helper methods to custom segments for domain-specific logic

### Avoid

- ❌ Don't use magic array indices when typed accessors are available
- ❌ Don't assume segments exist — wrap in conditionals
- ❌ Don't hardcode subIds — they vary by message type
- ❌ Don't modify library segment classes — extend with custom segments instead
- ❌ Don't parse raw values without checking types

---

## 🛠️ Development

### Commands

```bash
composer install                        # Install dependencies

# Testing
composer test                           # Run all tests

# Code Quality
composer quality                        # Run all checks
composer csfix                          # Fix code style
composer psalm                          # Static analysis (Psalm)
composer phpstan                        # Static analysis (PHPStan)
composer rector                         # Apply refactoring rules
```

### Code Standards

- PHP 8.0+, strict types, PSR-4 autoloading
- All code must pass PHP-CS-Fixer, Psalm, PHPStan, and Rector
- Type hints required for all methods
- Tests required for new functionality

---

## 🤝 Contributing

We welcome contributions of all kinds—bug fixes, ideas, and improvements.

- 🐛 [Report issues](https://github.com/Chemaclass/EdifactParser/issues)
- 🔧 [Submit a pull request](https://github.com/Chemaclass/EdifactParser/pulls)

📋 See the [contributing guide](.github/CONTRIBUTING.md) to get started.
