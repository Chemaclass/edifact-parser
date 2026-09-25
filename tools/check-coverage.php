<?php

declare(strict_types=1);

/**
 * Fails unless a Clover report covers every element. CI enforces 100% line coverage.
 *
 *   php tools/check-coverage.php coverage.xml
 */

$path = $argv[1] ?? 'coverage.xml';
$xml = is_file($path) ? simplexml_load_file($path) : false;

if ($xml === false) {
    fwrite(STDERR, "Cannot read a Clover report at '{$path}'\n");
    exit(2);
}

$metrics = $xml->project->metrics;
$elements = (int) $metrics['elements'];
$covered = (int) $metrics['coveredelements'];

printf("Coverage: %d/%d elements\n", $covered, $elements);

if ($covered !== $elements) {
    fwrite(STDERR, "Coverage is below 100%\n");
    exit(1);
}
