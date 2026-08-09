<?php

declare(strict_types=1);

/**
 * Compares two `benchmark.php --json` runs and fails when a metric regresses beyond the
 * threshold.
 *
 *   php tools/benchmark-compare.php base.json head.json [threshold]
 *
 * The threshold is deliberately loose (1.5x by default). This gate exists to catch an
 * algorithmic regression — the kind that took context grouping from linear to quadratic
 * and went unnoticed for several releases — not to police micro-noise on a shared runner.
 */

const DEFAULT_THRESHOLD = 1.5;
/** Below this, timings are dominated by scheduler noise rather than the code under test. */
const NOISE_FLOOR_MS = 5.0;

$baseFile = $argv[1] ?? null;
$headFile = $argv[2] ?? null;
$threshold = (float) ($argv[3] ?? DEFAULT_THRESHOLD);

if ($baseFile === null || $headFile === null) {
    fwrite(STDERR, "usage: benchmark-compare.php <base.json> <head.json> [threshold]\n");
    exit(2);
}

/**
 * @return array<string, float>
 */
function results(string $file): array
{
    $raw = @file_get_contents($file);

    if ($raw === false) {
        fwrite(STDERR, "cannot read {$file}\n");
        exit(2);
    }

    /** @var array{results?: array<string, float>} $decoded */
    $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

    return $decoded['results'] ?? [];
}

$base = results($baseFile);
$head = results($headFile);

if ($head === []) {
    fwrite(STDERR, "the head run produced no results\n");
    exit(2);
}

// An empty base is expected the first time this gate runs, or when the base predates a
// metric: everything reads as new and nothing can regress.
if ($base === []) {
    echo "No baseline to compare against — recording this run as the first.\n";
}

$regressions = [];

printf("%-24s %10s %10s %9s\n", 'metric', 'base', 'head', 'ratio');
echo str_repeat('-', 58), "\n";

foreach ($head as $name => $headMs) {
    $baseMs = $base[$name] ?? null;

    if ($baseMs === null) {
        printf("%-24s %10s %10.2f %9s   (new)\n", $name, '-', $headMs, '-');
        continue;
    }

    $ratio = $baseMs > 0.0 ? $headMs / $baseMs : 1.0;
    $tooSmallToJudge = $baseMs < NOISE_FLOOR_MS && $headMs < NOISE_FLOOR_MS;
    $regressed = !$tooSmallToJudge && $ratio > $threshold;

    printf(
        "%-24s %10.2f %10.2f %8.2fx%s\n",
        $name,
        $baseMs,
        $headMs,
        $ratio,
        $regressed ? '   REGRESSION' : ($tooSmallToJudge ? '   (below noise floor)' : ''),
    );

    if ($regressed) {
        $regressions[] = \sprintf('%s: %.2f ms -> %.2f ms (%.2fx)', $name, $baseMs, $headMs, $ratio);
    }
}

echo "\n";

if ($regressions !== []) {
    fwrite(STDERR, \sprintf("Performance regression beyond %.2fx:\n  - %s\n", $threshold, implode("\n  - ", $regressions)));
    exit(1);
}

printf("No metric regressed beyond %.2fx.\n", $threshold);
