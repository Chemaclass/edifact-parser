<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\EdifactParser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\GroupingRules;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\TransactionMessage;
use PHPUnit\Framework\TestCase;

final class EdifactParserTest extends TestCase
{
    /**
     * @test
     */
    public function parse_throws_when_the_content_has_parser_errors(): void
    {
        // Segments without terminators put the underlying parser into an error state.
        $this->expectException(InvalidFile::class);

        EdifactParser::createWithDefaultSegments()->parse("UNH+1+ORDERS\nUNT+2+1\n");
    }

    /**
     * @test
     */
    public function parse_file_throws_when_the_file_does_not_exist(): void
    {
        $this->expectException(InvalidFile::class);
        $this->expectExceptionMessage('File not found');

        EdifactParser::createWithDefaultSegments()->parseFile('/no/such/file.edi');
    }

    /**
     * @test
     */
    public function parse_file_throws_when_the_file_exists_but_is_not_readable(): void
    {
        if (posix_getuid() === 0) {
            self::markTestSkipped('Root can read files regardless of permissions.');
        }

        $path = tempnam(sys_get_temp_dir(), 'edi');
        self::assertIsString($path);
        chmod($path, 0000);

        try {
            $this->expectException(InvalidFile::class);
            $this->expectExceptionMessage('Unable to read file');

            EdifactParser::createWithDefaultSegments()->parseFile($path);
        } finally {
            chmod($path, 0600);
            unlink($path);
        }
    }

    /**
     * @test
     */
    public function default_parser_accepts_custom_grouping_rules(): void
    {
        $edi = "UNH+1+ORDERS:D:96A:UN'NAD+BY'CTA+IC'UNT+4+1'";

        $parser = EdifactParser::createWithDefaultSegments(GroupingRules::default()->withContextTags([]));

        self::assertSame([], $parser->parse($edi)->transactionMessages()[0]->contextSegments());
        // The no-argument form keeps building contexts.
        self::assertCount(
            1,
            EdifactParser::createWithDefaultSegments()->parse($edi)->transactionMessages()[0]->contextSegments(),
        );
    }

    /**
     * @test
     */
    public function segments_can_be_grouped_from_any_iterable(): void
    {
        $factory = SegmentFactory::withDefaultSegments();
        $rawSegments = [['UNH', '1', ['ORDERS', 'D', '96A', 'UN']], ['BGM', '220'], ['UNT', '3', '1']];

        $generator = (static function () use ($factory, $rawSegments) {
            foreach ($rawSegments as $raw) {
                yield $factory->createSegmentFromArray($raw);
            }
        })();

        $result = TransactionMessage::groupSegments(GroupingRules::default(), $generator);

        self::assertCount(1, $result->transactionMessages());
        self::assertSame('ORDERS', $result->transactionMessages()[0]->messageType());
    }
}
