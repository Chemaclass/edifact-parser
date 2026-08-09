<?php

declare(strict_types=1);

/**
 * Executable version of docs/llms/writing.md. Run by CI so the documentation cannot rot.
 */

use EdifactParser\EdifactParser;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\Qualifier\DTMQualifier;
use EdifactParser\Segments\Qualifier\NADQualifier;
use EdifactParser\Segments\Qualifier\PRIQualifier;
use EdifactParser\Segments\Qualifier\QTYQualifier;
use EdifactParser\Segments\Qualifier\RFFQualifier;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Serializer\EdifactSerializer;
use EdifactParser\Serializer\UnaSeparators;
use EdifactParser\Writer\InterchangeBuilder;
use EdifactParser\Writer\MessageBuilder;

require __DIR__ . '/../vendor/autoload.php';

// --- Builders ---------------------------------------------------------------
$nad = NADNameAddress::builder()
    ->withQualifier(NADQualifier::BUYER)
    ->withPartyId('123456')
    ->withName('ACME Corporation')
    ->withCity('Springfield')
    ->withCountryCode('US')
    ->build();

assert($nad->partyQualifier() === 'BY');
assert($nad->name() === 'ACME Corporation');

$qty = new QTYQuantity(['QTY', ['21', '100', 'PCE']]);
assert($qty->quantityAsFloat() === 100.0);

// --- Serializer -------------------------------------------------------------
$serializer = new EdifactSerializer();
$rendered = $serializer->serializeSegment($nad);
assert(str_starts_with($rendered, 'NAD+BY+123456'));
assert(str_ends_with($rendered, "'"));

$many = $serializer->serialize([$nad, $qty]);
assert(substr_count($many, "'") === 2);
assert(str_starts_with($serializer->serialize([$nad], includeUna: true), 'UNA'));

$custom = new EdifactSerializer(new UnaSeparators(component: '#', element: '|'));
assert(str_contains($custom->serializeSegment($qty), '#'));

// --- Interchange assembly ---------------------------------------------------
$messageBuilder = MessageBuilder::create('1', 'ORDERS')
    ->addSegment($nad)
    ->addSegment($qty);

$segments = $messageBuilder->build();
assert($segments[0]->tag() === 'UNH');
assert($segments[count($segments) - 1]->tag() === 'UNT');

$edi = InterchangeBuilder::create('SENDER', 'RECIPIENT', 'REF01')
    ->preparedAt('240101', '1200')
    ->addMessage($messageBuilder)
    ->toString();

assert(str_starts_with($edi, 'UNA'));

// It round-trips through the parser, which is the only proof that matters.
$reparsed = EdifactParser::createWithDefaultSegments()->parse($edi);
assert($reparsed->count() === 1);
assert($reparsed->transactionMessages()[0]->messageType() === 'ORDERS');
assert($reparsed->transactionMessages()[0]->segmentByTagAndSubId('NAD', 'BY')?->name() === 'ACME Corporation');

// --- Qualifier constants ----------------------------------------------------
assert(NADQualifier::BUYER === 'BY');
assert(QTYQualifier::ORDERED === '21');
assert(PRIQualifier::CALCULATION_NET === 'AAA');
assert(DTMQualifier::class !== '');
assert(RFFQualifier::class !== '');

echo "docs/llms/writing.md OK\n";
