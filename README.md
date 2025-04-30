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

Install via [Composer](https://getcomposer.org/):

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

$parserResult = EdifactParser::createWithDefaultSegments()->parse($fileContent);
$firstMessage = $parserResult->transactionMessages()[0];

$nadSegment = $firstMessage->segmentByTagAndSubId('NAD', 'CN');
$personName = $nadSegment->rawValues()[4]; // 'Person Name'
```

### 📂 More Examples

- [example/printing-segments.php](example/printing-segments.php) — Print all parsed segments line by line.
- [example/extracting-data.php](example/extracting-data.php) — Extract values from specific segments.

## 🤝 Contributing

We welcome contributions of all kinds—bug fixes, ideas, and improvements.

- 🐛 [Report issues](https://github.com/Chemaclass/EdifactParser/issues)
- 🔧 [Submit a pull request](https://github.com/Chemaclass/EdifactParser/pulls)

📋 See the [contributing guide](.github/CONTRIBUTING.md) to get started.
