<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use EdifactParser\EdifactParser;
use EdifactParser\Segments\SegmentInterface;

$fileContent = file_get_contents(__DIR__ . '/edifact-sample.edi');
$messages = EdifactParser::createWithDefaultSegments()->parse($fileContent);
$firstMessage = reset($messages);

/** @var SegmentInterface $cnNadSegment */
$cnNadSegment = $firstMessage->segmentByTagAndSubId('NAD', 'CN');
$personName = $cnNadSegment->rawValues()[4];
assert('Person Name' === $personName);
echo $personName . PHP_EOL;

