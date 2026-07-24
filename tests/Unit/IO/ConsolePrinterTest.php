<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\IO;

use EdifactParser\EdifactParser;
use EdifactParser\IO\ConsolePrinter;
use PHPUnit\Framework\TestCase;

final class ConsolePrinterTest extends TestCase
{
    /**
     * @test
     */
    public function prints_the_requested_segments_with_inline_context(): void
    {
        $message = EdifactParser::createWithDefaultSegments()
            ->parseFile(__DIR__ . '/../../../example/edifact-sample.edi')
            ->transactionMessages()[0];

        $printer = ConsolePrinter::createWithHeaders(['UNH', 'NAD']);

        ob_start();
        $printer->printMessage($message);
        $output = (string) ob_get_clean();

        // Header lines for each requested tag that exists in the message.
        self::assertStringContainsString("UNH:\n", $output);
        self::assertStringContainsString("NAD:\n", $output);
        // Segment body rendered as "<subId> |> <json>".
        self::assertStringContainsString('|>', $output);
    }

    /**
     * @test
     */
    public function skips_tags_that_are_absent_from_the_message(): void
    {
        $message = EdifactParser::createWithDefaultSegments()
            ->parseFile(__DIR__ . '/../../../example/edifact-sample.edi')
            ->transactionMessages()[0];

        $printer = ConsolePrinter::createWithHeaders(['ZZZ']);

        ob_start();
        $printer->printMessage($message);
        $output = (string) ob_get_clean();

        self::assertSame('', $output);
    }
}
