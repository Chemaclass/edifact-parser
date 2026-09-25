# EDIFACT Parser

[![Type Coverage](https://shepherd.dev/github/Chemaclass/EdifactParser/coverage.svg)](https://shepherd.dev/github/chemaclass/EdifactParser)
[![CI](https://github.com/Chemaclass/EdifactParser/workflows/CI/badge.svg?branch=main)](https://github.com/Chemaclass/EdifactParser/actions)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)

Parse, write, validate, and stream UN/EDIFACT in PHP 8.0+. New to the format? Read the [EDIFACT primer](docu/README.md).

## Install

```bash
composer require chemaclass/edifact-parser
```

Requires `ext-json` and `ext-mbstring`.

## Quick start

Save this as `order.php` after installing the package, then run `php order.php`:

```php
<?php

declare(strict_types=1);

use EdifactParser\EdifactParser;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;

require __DIR__ . '/vendor/autoload.php';

$edi = <<<'EDI'
    UNB+UNOC:3+SENDER+RECEIVER+240101:1200+1'
    UNH+1+ORDERS:D:96A:UN'
    BGM+220+PO-123+9'
    NAD+BY+123456++ACME Corporation+Street 1+Berlin++10115+DE'
    LIN+1++SKU-1:BP'
    QTY+21:100:PCE'
    UNT+6+1'
    UNZ+1+1'
    EDI;

$message = EdifactParser::createWithDefaultSegments()->parse($edi)->firstMessage();
$buyer = $message?->segmentOfType(NADNameAddress::class, 'BY');
$quantity = $message?->lineItemById(1)?->segmentOfType(QTYQuantity::class, '21');

printf("%s: %s, %.0f items\n", $message?->messageType(), $buyer?->name(), $quantity?->quantityAsFloat());
// ORDERS: ACME Corporation, 100 items
```

For a file, call `parseFile('/path/to/order.edi')` instead of `parse($edi)`. `firstMessage()` and typed lookups return `null` when the requested data is absent. The [runnable Quick Start](example/readme-quick-start.php) checks the output.

## Choose a task

| Task | Start here |
| --- | --- |
| Parse a string or file, handle errors, stream large files | [Parsing](docs/llms/parsing.md) |
| Find typed values, duplicates, line items, groups, or differences | [Reading data](docs/llms/reading.md) |
| Build segments or write a complete interchange | [Writing EDIFACT](docs/llms/writing.md) |
| Check message rules or directory constraints | [Validation](docs/llms/validation.md) |
| Inspect, validate, or compare files in a terminal | [Command line](docs/llms/cli.md) |
| Add segments, configure grouping, or discover accessors | [Extending](docs/llms/extending.md) |

All guides are listed in the [documentation index](docs/README.md). Their PHP examples have runnable counterparts in [`example/`](example). See [UPGRADING.md](UPGRADING.md) when moving from 6.x.

## Read a message

`parse()` and `parseFile()` return a `ParserResult`. Its `firstMessage()` method returns `null` when the interchange has no message. Typed lookups also return `null` when a segment is absent.

```php
$message = EdifactParser::createWithDefaultSegments()
    ->parseFile('/path/to/order.edi')
    ->firstMessage();

$buyer = $message?->segmentOfType(NADNameAddress::class, 'BY');
$quantity = $message?->lineItemById(1)?->segmentOfType(QTYQuantity::class, '21');
```

The default factory provides typed accessors for 32 common tags. Other tags remain readable through `rawValues()`. Use [directory segments](docs/llms/extending.md#directory-segments) to opt into 134 typed tags, or [register a custom segment](docs/llms/extending.md#custom-segments).

For repeated tags, use `$message->query()` or `$message->segments()` to keep every occurrence in order. Keyed lookups keep the last segment with the same tag and sub ID. See [reading data](docs/llms/reading.md#keyed-lookups-return-the-typed-segment).

## Use the command line

Composer installs `vendor/bin/edifact`:

```bash
vendor/bin/edifact parse order.edi
vendor/bin/edifact inspect order.edi
vendor/bin/edifact validate order.edi
vendor/bin/edifact diff before.edi after.edi
```

Results go to stdout as JSON. Errors go to stderr. Exit codes are `0` for success, `1` for invalid input or differences, and `2` for usage errors. See the [CLI guide](docs/llms/cli.md) for options and output shapes.

## Write and validate

Use [segment builders](docs/llms/writing.md#build-segments) to create typed segments. `EdifactSerializer` writes segments, and `InterchangeBuilder` fills in `UNT` and `UNZ` counts for a complete interchange. `MessageValidator` checks required tags, counts, and order; optional [directory validation](docs/llms/validation.md#directory-validation) checks elements and code lists.

The parser keeps the original bytes. When reading non-ASCII values, decode them using the `UNB` syntax identifier as shown in the [character set guide](docs/llms/reading.md#character-sets). The default `NativeTokenizer` preserves these bytes; the legacy `SabasTokenizer` strips them. See [tokenizers](docs/llms/parsing.md#tokenizers).

## Contributing

Run `composer test` and `composer examples` before opening a pull request. See the [contributing guide](.github/CONTRIBUTING.md) for the full workflow.
