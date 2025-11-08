<?php

declare(strict_types=1);

namespace EdifactParser\Tests\Unit\Analysis;

use EDI\Parser;
use EdifactParser\Analysis\MessageAnalyzer;
use EdifactParser\SegmentList;
use EdifactParser\TransactionMessage;
use PHPUnit\Framework\TestCase;

final class MessageAnalyzerTest extends TestCase
{
    /**
     * @test
     */
    public function get_message_type(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
UNT+5+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        self::assertEquals('ORDERS', $analyzer->getType());
    }

    /**
     * @test
     */
    public function segment_count(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
NAD+BY+123456'
NAD+SU+789012'
DTM+137:20240101:102'
UNT+5+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        self::assertEquals(5, $analyzer->segmentCount());
    }

    /**
     * @test
     */
    public function segment_count_by_tag(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
NAD+BY+123456'
NAD+SU+789012'
NAD+CN+456789'
DTM+137:20240101:102'
UNT+6+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        self::assertEquals(3, $analyzer->segmentCountByTag('NAD'));
        self::assertEquals(1, $analyzer->segmentCountByTag('DTM'));
        self::assertEquals(0, $analyzer->segmentCountByTag('QTY'));
    }

    /**
     * @test
     */
    public function line_item_count(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
LIN+1'
QTY+21:100'
LIN+2'
QTY+21:50'
LIN+3'
QTY+21:25'
UNT+8+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        self::assertEquals(3, $analyzer->lineItemCount());
    }

    /**
     * @test
     */
    public function address_count(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
NAD+BY+123456'
NAD+SU+789012'
NAD+CN+456789'
UNT+5+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        self::assertEquals(3, $analyzer->addressCount());
    }

    /**
     * @test
     */
    public function get_party_qualifiers(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
NAD+BY+123456'
NAD+SU+789012'
NAD+CN+456789'
UNT+5+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        self::assertEquals(['BY', 'SU', 'CN'], $analyzer->getPartyQualifiers());
    }

    /**
     * @test
     */
    public function get_currencies(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+INVOIC:D:96A:UN'
CUX+2:EUR:4'
CUX+3:USD:4'
UNT+4+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        self::assertEquals(['EUR', 'USD'], $analyzer->getCurrencies());
    }

    /**
     * @test
     */
    public function calculate_total_amount_without_qualifier(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+INVOIC:D:96A:UN'
MOA+79:1000.50'
MOA+125:200.25'
MOA+176:150.00'
UNT+5+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        self::assertEquals(1350.75, $analyzer->calculateTotalAmount());
    }

    /**
     * @test
     */
    public function calculate_total_amount_with_qualifier(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+INVOIC:D:96A:UN'
UNS+S'
MOA+79:1000.50'
MOA+125:200.25'
MOA+176:500.00'
UNT+6+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        // Only MOA segments with qualifier '79'
        self::assertEquals(1000.50, $analyzer->calculateTotalAmount('79'));

        // Only MOA segments with qualifier '125'
        self::assertEquals(200.25, $analyzer->calculateTotalAmount('125'));

        // Only MOA segments with qualifier '176'
        self::assertEquals(500.00, $analyzer->calculateTotalAmount('176'));
    }

    /**
     * @test
     */
    public function calculate_total_quantity_without_qualifier(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
UNS+S'
QTY+21:100'
QTY+12:50'
QTY+46:25.5'
UNT+6+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        self::assertEquals(175.5, $analyzer->calculateTotalQuantity());
    }

    /**
     * @test
     */
    public function calculate_total_quantity_with_qualifier(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
UNS+S'
QTY+21:100'
QTY+12:50'
QTY+46:75'
UNT+6+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        // Only QTY segments with qualifier '21' (ordered)
        self::assertEquals(100.0, $analyzer->calculateTotalQuantity('21'));

        // Only QTY segments with qualifier '12' (dispatched)
        self::assertEquals(50.0, $analyzer->calculateTotalQuantity('12'));

        // Only QTY segments with qualifier '46' (to be delivered)
        self::assertEquals(75.0, $analyzer->calculateTotalQuantity('46'));
    }

    /**
     * @test
     */
    public function get_summary(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
NAD+BY+123456'
NAD+SU+789012'
DTM+137:20240101:102'
UNS+S'
QTY+21:100'
PRI+AAA:99.99'
MOA+79:1000.00'
UNT+9+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);
        $summary = $analyzer->getSummary();

        self::assertEquals('ORDERS', $summary['message_type']);
        self::assertEquals(9, $summary['total_segments']);
        self::assertEquals(0, $summary['line_items']);
        self::assertEquals(2, $summary['addresses']);
        self::assertEquals(['BY', 'SU'], $summary['party_qualifiers']);

        self::assertEquals(2, $summary['segment_counts']['NAD']);
        self::assertEquals(0, $summary['segment_counts']['LIN']);
        self::assertEquals(1, $summary['segment_counts']['QTY']);
        self::assertEquals(1, $summary['segment_counts']['PRI']);
        self::assertEquals(1, $summary['segment_counts']['MOA']);
        self::assertEquals(1, $summary['segment_counts']['DTM']);
    }

    /**
     * @test
     */
    public function has_segment(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
NAD+BY+123456'
DTM+137:20240101:102'
UNT+4+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        self::assertTrue($analyzer->hasSegment('NAD'));
        self::assertTrue($analyzer->hasSegment('DTM'));
        self::assertFalse($analyzer->hasSegment('QTY'));
        self::assertFalse($analyzer->hasSegment('PRI'));
    }

    /**
     * @test
     */
    public function has_summary_section(): void
    {
        $messageWithSummary = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
LIN+1'
UNS+S'
MOA+79:1000.00'
UNT+5+1'
EDI
        );

        $messageWithoutSummary = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
LIN+1'
UNT+3+1'
EDI
        );

        self::assertTrue((new MessageAnalyzer($messageWithSummary))->hasSummarySection());
        self::assertFalse((new MessageAnalyzer($messageWithoutSummary))->hasSummarySection());
    }

    /**
     * @test
     */
    public function handles_empty_message(): void
    {
        $message = $this->parseMessage(
            <<<EDI
UNH+1+ORDERS:D:96A:UN'
UNT+2+1'
EDI
        );

        $analyzer = new MessageAnalyzer($message);

        self::assertEquals('ORDERS', $analyzer->getType());
        self::assertEquals(2, $analyzer->segmentCount());
        self::assertEquals(0, $analyzer->lineItemCount());
        self::assertEquals(0, $analyzer->addressCount());
        self::assertEquals([], $analyzer->getPartyQualifiers());
        self::assertEquals([], $analyzer->getCurrencies());
        self::assertEquals(0.0, $analyzer->calculateTotalAmount());
        self::assertEquals(0.0, $analyzer->calculateTotalQuantity());
    }

    private function parseMessage(string $edifactContent): TransactionMessage
    {
        $parser = (new Parser())->loadString($edifactContent);
        $segments = SegmentList::withDefaultFactory()->fromRaw($parser->get());
        $result = TransactionMessage::groupSegmentsByMessage(...$segments);

        return $result->transactionMessages()[0];
    }
}
