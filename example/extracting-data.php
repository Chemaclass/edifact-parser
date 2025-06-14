<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use EdifactParser\EdifactParser;
use EdifactParser\Segments\SegmentInterface;

$parserResult = EdifactParser::createWithDefaultSegments()->parseFile(__DIR__ . '/edifact-sample.edi');
$firstMessage = $parserResult->transactionMessages()[0];

/** @var SegmentInterface $cnNadSegment */
$cnNadSegment = $firstMessage->segmentByTagAndSubId('NAD', 'CN');
$personName = $cnNadSegment->rawValues()[4];
assert('Person Name' === $personName);
echo $personName . PHP_EOL;

