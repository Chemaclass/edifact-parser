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

- [example/printing-segments.php](example/printing-segments.php) — Print all parsed segments line by line.
- [example/extracting-data.php](example/extracting-data.php) — Extract values from specific segments.
- [example/context-segments.php](example/context-segments.php) — Traverse hierarchical context segments.

---

## 📖 Usage Guide

### Accessing Segments

```php
// Direct lookup by tag and subId (fastest)
$nadSegment = $message->segmentByTagAndSubId('NAD', 'BY');

// Get all segments with the same tag
$allNadSegments = $message->segmentsByTag('NAD');

// Always null-check when accessing segments
if ($nadSegment) {
    $companyName = $nadSegment->rawValues()[4];
}
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

## ✅ Best Practices

### Do

- ✅ Always null-check segments — not all segments exist in every message
- ✅ Use `segmentByTagAndSubId()` for single lookups, `segmentsByTag()` for multiple
- ✅ Check field types before accessing — some `rawValues()` fields are arrays
- ✅ Use line items for order/invoice processing — cleaner than manual grouping
- ✅ Add helper methods to custom segments for domain-specific logic

### Avoid

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
