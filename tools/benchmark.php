<?php

declare(strict_types=1);

/**
 * Benchmarks the paths AGENTS.md marks as hot.
 *
 *   php tools/benchmark.php            human-readable table
 *   php tools/benchmark.php --json     machine-readable, for tools/benchmark-compare.php
 *
 * The corpus is generated deterministically at runtime, so nothing large lives in the
 * repo and every run measures the same input. Never change the generator in a commit
 * that also reports a performance delta — the numbers stop being comparable.
 */

namespace EdifactParser\Tools;

use EdifactParser\Analysis\MessageAnalyzer;
use EdifactParser\EdifactParser;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Serializer\EdifactSerializer;
use EdifactParser\StreamingParser;
use EdifactParser\Tokenizer\NativeTokenizer;
use EdifactParser\Tokenizer\SabasTokenizer;
use EdifactParser\Validation\MessageRuleSets;
use EdifactParser\Validation\MessageValidator;

use function count;
use function in_array;
use function strlen;

require __DIR__ . '/../vendor/autoload.php';

const CORPUS_MESSAGES = 120;
const CORPUS_LINE_ITEMS = 40;
const WIDE_LINE_ITEMS = 3000;
const RUNS = 3;

/**
 * @return array{0: string, 1: string} [interchange, path to the same content on disk]
 */
function corpus(): array
{
    $lines = ["UNA:+.? '", "UNB+UNOC:3+SENDER+RECIPIENT+240101:1200+REF01'"];

    for ($message = 1; $message <= CORPUS_MESSAGES; ++$message) {
        $lines[] = "UNH+{$message}+ORDERS:D:96A:UN'";
        $lines[] = "BGM+220+ORD{$message}+9'";
        $lines[] = "DTM+137:20240101:102'";
        $lines[] = "NAD+BY+BUYER{$message}::9+++Some Street 1+Berlin++10115+DE'";
        $lines[] = "CTA+IC+:John Doe'";
        $lines[] = "COM+john@example.com:EM'";
        $lines[] = "NAD+SU+SUPPLIER{$message}::9+++Other Street 2+Munich++80331+DE'";
        $lines[] = "CUX+2:EUR:9'";

        for ($item = 1; $item <= CORPUS_LINE_ITEMS; ++$item) {
            $lines[] = "LIN+{$item}++ART{$item}:BP'";
            $lines[] = "PIA+1+ART{$item}:SA'";
            $lines[] = "IMD+F++:::Product {$item} description'";
            $lines[] = 'QTY+21:' . ($item * 10) . ":PCE'";
            $lines[] = 'PRI+AAA:' . ($item * 1.5) . "'";
            $lines[] = 'MOA+203:' . ($item * 15) . "'";
        }

        $lines[] = "UNS+S'";
        $lines[] = "MOA+79:12345'";
        $lines[] = 'CNT+2:' . CORPUS_LINE_ITEMS . "'";
        $lines[] = 'UNT+' . (CORPUS_LINE_ITEMS * 6 + 12) . "+{$message}'";
    }

    $lines[] = 'UNZ+' . CORPUS_MESSAGES . "+REF01'";
    $content = implode("\n", $lines);

    $path = sys_get_temp_dir() . '/edifact-benchmark-corpus.edi';
    file_put_contents($path, $content);

    return [$content, $path];
}

/**
 * A single message with many line items — the shape that exposed quadratic grouping.
 */
function wideMessage(): string
{
    $lines = ["UNB+UNOC:3+S+R+240101:1200+1'", "UNH+1+ORDERS:D:96A:UN'", "BGM+220'"];

    for ($item = 1; $item <= WIDE_LINE_ITEMS; ++$item) {
        $lines[] = "LIN+{$item}++ART{$item}:BP'";
        $lines[] = "QTY+21:{$item}'";
    }

    $lines[] = "UNS+S'";
    $lines[] = "UNT+9+1'";
    $lines[] = "UNZ+1+1'";

    return implode("\n", $lines);
}

function best(callable $subject): float
{
    $best = INF;

    for ($run = 0; $run < RUNS; ++$run) {
        $started = hrtime(true);
        $subject();
        $best = min($best, (hrtime(true) - $started) / 1e6);
    }

    return $best;
}

[$content, $path] = corpus();
$wide = wideMessage();
$messages = EdifactParser::createWithDefaultSegments()->parse($content)->transactionMessages();

$benchmarks = [
    'factory-boot' => static function (): void {
        for ($i = 0; $i < 200; ++$i) {
            SegmentFactory::withDefaultSegments();
        }
    },
    // Both tokenizers are named explicitly: 'parse-default' would silently change meaning
    // the moment the default changes, and the two rows would stop being comparable.
    'parse-sabas' => static fn () => EdifactParser::createWithDefaultSegments(
        tokenizer: new SabasTokenizer()
    )->parse($content),
    'parse-native' => static fn () => EdifactParser::createWithDefaultSegments(
        tokenizer: new NativeTokenizer()
    )->parse($content),
    'tokenize-sabas' => static fn () => (new SabasTokenizer())->tokenize($content),
    'tokenize-native' => static fn () => (new NativeTokenizer())->tokenize($content),
    'parse-many-line-items' => static fn () => EdifactParser::createWithDefaultSegments()->parse($wide),
    'stream-parse' => static function () use ($path): void {
        foreach (StreamingParser::createWithDefaultSegments()->parseFile($path) as $message) {
            // draining the generator is the work being measured
        }
    },
    'validate' => static function () use ($messages): void {
        $validator = new MessageValidator();
        $rules = MessageRuleSets::orders();
        foreach ($messages as $message) {
            $validator->validate($message, $rules);
        }
    },
    'analyze' => static function () use ($messages): void {
        foreach ($messages as $message) {
            (new MessageAnalyzer($message))->getSummary();
        }
    },
    'serialize' => static function () use ($messages): void {
        $serializer = new EdifactSerializer();
        foreach ($messages as $message) {
            $serializer->serialize($message);
        }
    },
];

$results = [];
foreach ($benchmarks as $name => $subject) {
    $results[$name] = round(best($subject), 2);
}

@unlink($path);

$asJson = in_array('--json', $argv, true);

if ($asJson) {
    echo json_encode([
        'php' => PHP_VERSION,
        'segments' => count((new NativeTokenizer())->tokenize($content)),
        'results' => $results,
    ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), "\n";

    exit(0);
}

printf("corpus: %.2f MB, %d segments, best of %d\n\n", strlen($content) / 1048576, count((new NativeTokenizer())->tokenize($content)), RUNS);
foreach ($results as $name => $ms) {
    printf("  %-24s %9.2f ms\n", $name, $ms);
}
