# EDIFACT Parser

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/?branch=master)
[![Type Coverage](https://shepherd.dev/github/Chemaclass/EdifactParser/coverage.svg)](https://shepherd.dev/github/chemaclass/EdifactParser)
[![CI](https://github.com/Chemaclass/EdifactParser/workflows/CI/badge.svg?branch=master)](https://github.com/Chemaclass/EdifactParser/actions)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)

EDIFACT stands for `Electronic Data Interchange For Administration, Commerce, and Transport`. 

This repository contains a parser for any EDIFACT file to extract the values from any segment
defined in an EDIFACT formatted file. 

Ok, but... [What is EDIFACT?](/docu/README.md)

## Format of an EDIFACT file

* Each line of the file consists of a set of data that belongs to a specific segment of a message.

* A segment is defined by a tag. Following the rest of the data that belongs to that segment. More about segments [here](/docu/segments/README.md).

* A message is a list of segments. Usually, all segments between the UNH and UNT segments compound a message.

* A transaction is the list of messages that belongs to a file. 

### Installation

```bash
composer require chemaclass/edifact-parser
```

### Contribute

You are more than welcome to contribute reporting
[issues](https://github.com/gacela-project/gacela/issues),
sharing [ideas](https://github.com/gacela-project/gacela/discussions),
or [contributing](.github/CONTRIBUTING.md) with your Pull Requests.

### Basic examples

You can see a full example of [printing segments](example/printing-segments.php).

You can see a full example of [extracting data](example/extracting-data.php).

```php
<?php declare(strict_types=1);

use EdifactParser\EdifactParser;
use EdifactParser\Segments\NADNameAddress;

$fileContent = <<<EDI
UNA:+.? '
UNB+UNOC:3+9457386:30+73130012:30+19101:118+8+MPM 2.19+1424'

UNH+1+IFTMIN:S:93A:UN:PN001'
TDT+20'
NAD+CZ+0410106314:160:Z12++Company Centre+c/o Carrier AB+City1++12345+DE'
NAD+CN+++Person Name+Street Nr 2+City2++12345+DE'
UNT+18+1'

UNZ+2+8'
EDI;

$parserResult = EdifactParser::createWithDefaultSegments()->parse($fileContent);
$firstMessage = $parserResult->transactionMessages()[0];

$cnNadSegment = $firstMessage->segmentByTagAndSubId('NAD', 'CN');
$personName = $cnNadSegment->rawValues()[4];

var_dump($personName); // 'Person Name'
```

