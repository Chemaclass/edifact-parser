# EDIFACT Parser

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/build-status/master)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg?style=flat-square)](https://php.net/)

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

## Development and contribution

### Your first try!

1. Clone/Fork the project and `cd` inside the repository
2. `docker-compose up`
3. `make bash` or `docker exec -ti -u dev edifact_parser_php bash` 
4. `composer install`
5. `php example.php`

## Basic example

You can see a full example of usage [here](example.php).

```php
<?php declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use EdifactParser\EdifactParser;

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

/** @psalm-var list<TransactionMessage> $messages */
$messages = EdifactParser::create()->parse($fileContent);
```

