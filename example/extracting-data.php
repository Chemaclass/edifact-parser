<?php

declare(strict_types=1);

require \dirname(__DIR__) . '/vendor/autoload.php';

use EdifactParser\EdifactParser;
use EdifactParser\Segments\NADNameAddress;

$parserResult = EdifactParser::createWithDefaultSegments()->parseFile(__DIR__ . '/edifact-sample.edi');
$firstMessage = $parserResult->transactionMessages()[0];

echo "=== Traditional Access ===\n";
// Direct access by tag and subId
$cnNadSegment = $firstMessage->segmentByTagAndSubId('NAD', 'CN');
$personName = $cnNadSegment->rawValues()[4];
echo "Person Name (raw): {$personName}\n\n";

echo "=== Using Typed Accessors ===\n";
// Better: Use typed accessors (ensure we have NADNameAddress type)
if ($cnNadSegment instanceof NADNameAddress) {
    $personName = $cnNadSegment->name();
    $street = $cnNadSegment->street();
    $city = $cnNadSegment->city();
    $postalCode = $cnNadSegment->postalCode();
    $country = $cnNadSegment->countryCode();
} else {
    // Fallback to raw values
    $personName = $cnNadSegment->rawValues()[4];
    $street = $cnNadSegment->rawValues()[5] ?? '';
    $city = $cnNadSegment->rawValues()[6] ?? '';
    $postalCode = $cnNadSegment->rawValues()[8] ?? '';
    $country = $cnNadSegment->rawValues()[9] ?? '';
}

echo "Name: {$personName}\n";
echo "Street: {$street}\n";
echo "City: {$city}\n";
echo "Postal Code: {$postalCode}\n";
echo "Country: {$country}\n\n";

echo "=== Using Fluent Query API ===\n";
// Best: Use fluent query for complex filtering
$consignee = $firstMessage->query()
    ->withTag('NAD')
    ->withSubId('CN')
    ->first();

if ($consignee instanceof NADNameAddress) {
    echo "Consignee: {$consignee->name()}\n";
    echo "Full address: {$consignee->street()}, {$consignee->city()} {$consignee->postalCode()}, {$consignee->countryCode()}\n\n";
}

// Get all NAD segments
echo "=== All Addresses ===\n";
$addresses = $firstMessage->query()
    ->withTag('NAD')
    ->get();

foreach ($addresses as $address) {
    if ($address instanceof NADNameAddress) {
        echo "{$address->subId()}: {$address->name()} ({$address->city()})\n";
    }
}

// Transform to get just names
echo "\n=== Company Names Only ===\n";
$companyNames = $firstMessage->query()
    ->withTag('NAD')
    ->map(static fn($segment) => $segment instanceof NADNameAddress
        ? $segment->name()
        : $segment->rawValues()[4] ?? 'N/A');

foreach ($companyNames as $name) {
    echo "- {$name}\n";
}

\assert($personName === 'Person Name');
