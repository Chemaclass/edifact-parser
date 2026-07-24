<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Exception;

use EdifactParser\Exception\InvalidFile;
use PHPUnit\Framework\TestCase;

final class InvalidFileTest extends TestCase
{
    /**
     * @test
     */
    public function with_errors_exposes_the_errors_and_message(): void
    {
        $exception = InvalidFile::withErrors(['boom', 'bang']);

        self::assertSame(['boom', 'bang'], $exception->getErrors());
        self::assertSame([], $exception->getContext());
        self::assertStringContainsString('Errors found while parsing the file', $exception->getMessage());
        self::assertStringContainsString('boom', $exception->getMessage());
        self::assertStringNotContainsString('Context:', $exception->getMessage());
    }

    /**
     * @test
     */
    public function with_context_includes_the_formatted_context_in_the_message(): void
    {
        $exception = InvalidFile::withContext(
            ['bad segment'],
            ['line' => 3, 'segment' => 'NAD', 'raw' => ['NAD', 'CN']],
        );

        self::assertSame(['bad segment'], $exception->getErrors());
        self::assertSame(['line' => 3, 'segment' => 'NAD', 'raw' => ['NAD', 'CN']], $exception->getContext());

        $message = $exception->getMessage();
        self::assertStringContainsString('Context:', $message);
        self::assertStringContainsString('line: 3', $message);           // scalar value
        self::assertStringContainsString('segment: NAD', $message);      // scalar value
        self::assertStringContainsString('raw: ["NAD","CN"]', $message); // non-scalar json encoded
        self::assertStringContainsString('bad segment', $message);
    }
}
