<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Functional;

use EdifactParser\EdifactParser;
use PHPUnit\Framework\TestCase;

final class ParseFileTest extends TestCase
{
    /** @test */
    public function parse_file_from_path(): void
    {
        $filepath = __DIR__ . '/../../example/edifact-sample.edi';

        $parserResult = EdifactParser::createWithDefaultSegments()->parseFile($filepath);

        self::assertNotEmpty($parserResult->transactionMessages());
    }
}
