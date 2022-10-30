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

## Installation as vendor

Using composer: ```composer require chemaclass/edifact-parser```

## Development

### Requirements

Optimally using [docker](./devops/dev/php.dockerfile) you will have everything you need for the development.

### Setup

Clone/Fork the project and go inside the repository.
There you can use docker-compose to create and run the docker image.
Go inside the container and run composer install to install all dependencies.
You can easily check is working running the example code.

```bash
docker-compose up
docker exec -ti -u dev edifact_parser_php bash 
composer install
php example/extracting-data.php
php example/printing-segments.php
```

### Composer scripts

```bash
composer test      # execute phpunit tests
composer csfix     # run php-cs-fixer fix
composer psalm     # display psalm errors
```

## Basic examples

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
... etc
UNT+18+1'
UNZ+2+8'
EDI;

$messages = EdifactParser::createWithDefaultSegments()->parse($fileContent);
$firstMessage = reset($messages);
$cnNadSegment = $firstMessage->segmentByTagAndSubId(NADNameAddress::class, 'CN');
$personName = $cnNadSegment->rawValues()[4]; // 'Person Name'
```

