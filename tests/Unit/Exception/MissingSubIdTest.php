<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Exception;

use EdifactParser\Exception\MissingSubId;
use PHPUnit\Framework\TestCase;

final class MissingSubIdTest extends TestCase
{
    /**
     * @test
     */
    public function message_keeps_the_raw_values_when_they_are_not_utf8(): void
    {
        $exception = new MissingSubId('CN', ['NAD', "M\xFCller"]);

        self::assertStringContainsString('"NAD"', $exception->getMessage());
        self::assertStringContainsString('"M\\ufffdller"', $exception->getMessage());
    }
}
