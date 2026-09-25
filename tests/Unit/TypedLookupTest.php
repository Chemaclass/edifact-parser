<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit;

use EdifactParser\EdifactParser;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\TransactionMessage;
use PHPUnit\Framework\TestCase;

/**
 * Calling accessors straight off the result is the point: Psalm runs over this file, so
 * it fails if the lookups stop returning the requested type.
 */
final class TypedLookupTest extends TestCase
{
    private const ORDER = <<<'EDI'
        UNB+UNOC:3+SENDER+RECIPIENT+20240101:1200+REF01'
        UNH+1+ORDERS:D:96A:UN'
        BGM+220+ORD1+9'
        NAD+BY+++First Buyer'
        NAD+BY+++Second Buyer'
        NAD+SU+++Supplier'
        LIN+1++ART1:BP'
        QTY+21:100'
        UNS+S'
        UNT+9+1'
        UNZ+1+REF01'
        EDI;

    /**
     * @test
     */
    public function segment_of_type_returns_the_requested_class(): void
    {
        self::assertSame('Supplier', $this->message()->segmentOfType(NADNameAddress::class, 'SU')?->name());
    }

    /**
     * @test
     */
    public function segment_of_type_resolves_duplicates_like_the_keyed_lookup(): void
    {
        $message = $this->message();

        self::assertSame(
            $message->segmentByTagAndSubId('NAD', 'BY'),
            $message->segmentOfType(NADNameAddress::class, 'BY'),
        );
        self::assertSame('Second Buyer', $message->segmentOfType(NADNameAddress::class, 'BY')?->name());
    }

    /**
     * @test
     */
    public function segment_of_type_finds_line_item_segments_by_numeric_sub_id(): void
    {
        $message = $this->message();

        self::assertSame(100.0, $message->segmentOfType(QTYQuantity::class, 21)?->quantityAsFloat());
        self::assertSame(100.0, $message->lineItems()[1]->segmentOfType(QTYQuantity::class, '21')?->quantityAsFloat());
    }

    /**
     * @test
     */
    public function segment_of_type_is_null_when_nothing_matches(): void
    {
        $message = $this->message();

        self::assertNull($message->segmentOfType(NADNameAddress::class, 'DP'));
        self::assertNull($message->segmentOfType(QTYQuantity::class, 'BY'));
    }

    /**
     * @test
     */
    public function segment_of_type_works_on_the_whole_result(): void
    {
        $result = EdifactParser::createWithDefaultSegments()->parse(self::ORDER);

        self::assertSame('Supplier', $result->segmentOfType(NADNameAddress::class, 'SU')?->name());
    }

    /**
     * @test
     */
    public function of_type_narrows_the_query_element_type(): void
    {
        $names = $this->message()->query()
            ->ofType(NADNameAddress::class)
            ->map(static fn (NADNameAddress $nad): string => $nad->name());

        self::assertSame(['First Buyer', 'Second Buyer', 'Supplier'], $names);
        self::assertSame('First Buyer', $this->message()->query()->ofType(NADNameAddress::class)->first()?->name());
    }

    private function message(): TransactionMessage
    {
        $message = EdifactParser::createWithDefaultSegments()->parse(self::ORDER)->firstMessage();
        self::assertNotNull($message);

        return $message;
    }
}
