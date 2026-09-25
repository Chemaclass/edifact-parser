<?php

declare(strict_types=1);

/**
 * Runs every example/*.php with assertions enabled, as the documentation CI job does.
 *
 *   composer examples
 */

$root = \dirname(__DIR__);
$failed = [];

foreach (glob($root . '/example/*.php') ?: [] as $file) {
    $name = substr($file, \strlen($root) + 1);
    $command = \sprintf(
        '%s -d zend.assertions=1 -d assert.exception=1 %s',
        escapeshellarg(PHP_BINARY),
        escapeshellarg($file),
    );

    exec($command . ' 2>&1', $output, $status);

    if ($status === 0) {
        fwrite(STDOUT, "ok   {$name}\n");
    } else {
        fwrite(STDOUT, "FAIL {$name}\n" . implode("\n", $output) . "\n");
        $failed[] = $name;
    }
    $output = [];
}

exit($failed === [] ? 0 : 1);
