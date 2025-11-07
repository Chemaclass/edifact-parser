<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use EdifactParser\EdifactParser;
use EdifactParser\Segments\NADNameAddress;

$filepath = $argv[1] ?? __DIR__ . '/edifact-sample.edi';
$parserResult = EdifactParser::createWithDefaultSegments()->parseFile($filepath);

foreach ($parserResult->transactionMessages() as $i => $message) {
    echo "======================================\n";
    echo "Message #{$i}: {$message->messageType()}\n";
    echo "======================================\n\n";

    // Example 1: Find specific segment
    echo "1. Find first consignee:\n";
    $consignee = $message->query()
        ->withTag('NAD')
        ->withSubId('CN')
        ->first();

    if ($consignee instanceof NADNameAddress) {
        echo "   → {$consignee->name()}\n\n";
    } elseif ($consignee) {
        echo "   → {$consignee->rawValues()[4]}\n\n";
    }

    // Example 2: Filter by multiple tags
    echo "2. Get all NAD and DTM segments:\n";
    $segments = $message->query()
        ->withTags(['NAD', 'DTM'])
        ->get();
    echo "   → Found {$message->query()->withTags(['NAD', 'DTM'])->count()} segments\n\n";

    // Example 3: Filter by type
    echo "3. Get all address segments:\n";
    $addresses = $message->query()
        ->withTag('NAD')
        ->get();

    foreach ($addresses as $address) {
        if ($address instanceof NADNameAddress) {
            echo "   → {$address->subId()}: {$address->name()}\n";
        }
    }
    echo "\n";

    // Example 4: Custom filtering with where()
    echo "4. Find German addresses:\n";
    $germanAddresses = $message->query()
        ->withTag('NAD')
        ->where(function($s) {
            if ($s instanceof NADNameAddress) {
                return $s->countryCode() === 'DE';
            }
            return ($s->rawValues()[9] ?? '') === 'DE';
        })
        ->get();

    echo "   → Found " . count($germanAddresses) . " German addresses\n\n";

    // Example 5: Limit and pagination
    echo "5. Get first 2 segments:\n";
    $limited = $message->query()
        ->withTag('NAD')
        ->limit(2)
        ->get();
    echo "   → Limited to " . count($limited) . " segments\n\n";

    // Example 6: Transform with map()
    echo "6. Extract all company names:\n";
    $names = $message->query()
        ->withTag('NAD')
        ->map(function($s) {
            return $s instanceof NADNameAddress ? $s->name() : $s->rawValues()[4] ?? 'N/A';
        });

    foreach ($names as $name) {
        echo "   - {$name}\n";
    }
    echo "\n";

    // Example 7: Check existence
    echo "7. Check if summary section exists:\n";
    $hasUns = $message->query()->withTag('UNS')->exists();
    echo "   → UNS segment exists: " . ($hasUns ? 'Yes' : 'No') . "\n\n";

    // Example 8: Count segments
    echo "8. Segment statistics:\n";
    echo "   → Total NAD segments: {$message->query()->withTag('NAD')->count()}\n";
    echo "   → Total MEA segments: {$message->query()->withTag('MEA')->count()}\n";
    echo "   → Total CNT segments: {$message->query()->withTag('CNT')->count()}\n\n";

    // Example 9: Iterate with each()
    echo "9. Process each NAD segment:\n";
    $message->query()
        ->withTag('NAD')
        ->each(function($segment) {
            $name = $segment instanceof NADNameAddress ? $segment->name() : $segment->rawValues()[4] ?? 'N/A';
            echo "   → Processing {$segment->subId()}: {$name}\n";
        });
    echo "\n";

    // Example 10: Complex chained query
    echo "10. Complex query - German companies only:\n";
    $germanCompanies = $message->query()
        ->withTag('NAD')
        ->where(function($s) {
            if ($s instanceof NADNameAddress) {
                return $s->countryCode() === 'DE' && !empty($s->name());
            }
            return false;
        })
        ->limit(5)
        ->get();

    foreach ($germanCompanies as $company) {
        if ($company instanceof NADNameAddress) {
            echo "   → {$company->name()} - {$company->city()}\n";
        }
    }

    echo "\n" . str_repeat("=", 50) . "\n\n";
}
