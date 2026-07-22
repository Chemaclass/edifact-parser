<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\EdifactParser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\StreamingParser;
use PHPUnit\Framework\TestCase;

use function count;

final class StreamingParserTest extends TestCase
{
    private const SAMPLE = __DIR__ . '/../../example/edifact-sample.edi';

    /**
     * @test
     */
    public function streams_the_same_messages_as_the_batch_parser(): void
    {
        $batch = EdifactParser::createWithDefaultSegments()->parseFile(self::SAMPLE)->transactionMessages();

        $streamed = [];
        foreach (StreamingParser::createWithDefaultSegments()->parseFile(self::SAMPLE) as $message) {
            $streamed[] = $message;
        }

        self::assertCount(count($batch), $streamed);
        self::assertEquals($batch[0]->allSegments(), $streamed[0]->allSegments());
        self::assertEquals($batch[1]->allSegments(), $streamed[1]->allSegments());
    }

    /**
     * @test
     */
    public function parsing_is_lazy_and_defers_io_until_iterated(): void
    {
        // Building the stream must not open the file yet (the generator body,
        // including fopen, runs only on the first iteration).
        $stream = StreamingParser::createWithDefaultSegments()->parseFile('/no/such/edifact/file.edi');

        $this->expectException(InvalidFile::class);
        foreach ($stream as $message) {
            // unreachable — the missing file throws as soon as iteration starts
        }
    }

    /**
     * @test
     */
    public function streams_a_large_interchange_message_by_message(): void
    {
        $oneMessage = "UNH+1+ORDERS:D:96A:UN'BGM+220'DTM+10:20191011:102'UNT+3+1'";
        $path = (string) tempnam(sys_get_temp_dir(), 'edi');
        file_put_contents($path, "UNB+UNOC:3+S+R+20191011:1200+REF'" . str_repeat($oneMessage, 5000) . "UNZ+5000+REF'");

        try {
            $count = 0;
            foreach (StreamingParser::createWithDefaultSegments()->parseFile($path) as $message) {
                ++$count;
            }
            self::assertSame(5000, $count);
        } finally {
            @unlink($path);
        }
    }

    /**
     * @test
     */
    public function honours_a_leading_una_with_custom_delimiters(): void
    {
        // Custom segment terminator '~' declared by the UNA service-string advice.
        $edi = 'UNA:+.? ~UNB+UNOC:3+S+R+200101:1200+1~UNH+1+ORDERS:D:96A:UN~BGM+220~UNT+3+1~UNZ+1+1~';
        $path = (string) tempnam(sys_get_temp_dir(), 'edi');
        file_put_contents($path, $edi);

        try {
            $messages = [];
            foreach (StreamingParser::createWithDefaultSegments()->parseFile($path) as $message) {
                $messages[] = $message;
            }

            self::assertCount(1, $messages);
            self::assertSame('ORDERS', $messages[0]->messageType());
        } finally {
            @unlink($path);
        }
    }
}
