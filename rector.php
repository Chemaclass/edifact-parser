<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/example',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets(php80: true)
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0);
