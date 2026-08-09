<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Tokenizer;

use EdifactParser\EdifactParser;
use EdifactParser\Exception\InvalidFile;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Tokenizer\SabasTokenizer;
use PHPUnit\Framework\TestCase;

final class SabasTokenizerTest extends TestCase
{
    /**
     * @test
     */
    public function tokenizes_a_well_formed_interchange(): void
    {
        $segments = (new SabasTokenizer())->tokenize("UNH+1+ORDERS:D:96A:UN'BGM+220'UNT+3+1'");

        self::assertSame([
            ['UNH', '1', ['ORDERS', 'D', '96A', 'UN']],
            ['BGM', '220'],
            ['UNT', '3', '1'],
        ], $segments);
    }

    /**
     * @test
     */
    public function errors_raised_while_parsing_now_surface(): void
    {
        // Up to 6.x errors() was read before get(), which is where the per-segment work
        // happens — so this was silently accepted and the data was returned mangled.
        $this->expectException(InvalidFile::class);
        $this->expectExceptionMessage('Non-printable character');

        (new SabasTokenizer())->tokenize("NAD+BY+++Müller GmbH'");
    }

    /**
     * @test
     */
    public function unterminated_content_is_rejected(): void
    {
        $this->expectException(InvalidFile::class);
        $this->expectExceptionMessage('without terminators');

        (new SabasTokenizer())->tokenize("UNH+1+ORDERS\nUNT+2+1\n");
    }

    /**
     * @test
     */
    public function an_unescaped_release_character_is_rejected(): void
    {
        $this->expectException(InvalidFile::class);

        (new SabasTokenizer())->tokenize("COM+john?@example.com:EM'");
    }

    /**
     * @test
     */
    public function it_remains_available_as_an_explicit_choice(): void
    {
        $parser = new EdifactParser(SegmentFactory::withDefaultSegments(), tokenizer: new SabasTokenizer());

        $result = $parser->parse("UNH+1+ORDERS:D:96A:UN'BGM+220'UNT+3+1'");

        self::assertSame('ORDERS', $result->transactionMessages()[0]->messageType());
    }
}
