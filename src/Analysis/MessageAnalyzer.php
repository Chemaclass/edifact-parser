<?php

declare(strict_types=1);

namespace EdifactParser\Analysis;

use EdifactParser\Segments\CUXCurrencyDetails;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\TransactionMessage;

use function count;
use function is_array;

/**
 * Analyzes EDIFACT messages to extract statistics and insights
 */
final class MessageAnalyzer
{
    public function __construct(private TransactionMessage $message)
    {
    }

    /**
     * Get message type (e.g., 'ORDERS', 'INVOIC', 'DESADV')
     */
    public function getType(): ?string
    {
        return $this->message->messageType();
    }

    /**
     * Count total segments in the message
     */
    public function segmentCount(): int
    {
        return $this->message->query()->count();
    }

    /**
     * Count segments by tag
     */
    public function segmentCountByTag(string $tag): int
    {
        return $this->message->query()->withTag($tag)->count();
    }

    /**
     * Count line items
     */
    public function lineItemCount(): int
    {
        return count($this->message->lineItems());
    }

    /**
     * Count NAD (address) segments
     */
    public function addressCount(): int
    {
        return $this->segmentCountByTag('NAD');
    }

    /**
     * Get all unique party qualifiers from NAD segments
     *
     * @return list<string>
     */
    public function getPartyQualifiers(): array
    {
        return $this->message->query()
            ->withTag('NAD')
            ->map(static function ($segment) {
                if ($segment instanceof NADNameAddress) {
                    return $segment->partyQualifier();
                }
                return $segment->rawValues()[1] ?? '';
            });
    }

    /**
     * Get all unique currencies from CUX segments
     *
     * @return list<string>
     */
    public function getCurrencies(): array
    {
        $currencies = [];

        $this->message->query()
            ->withTag('CUX')
            ->each(static function ($segment) use (&$currencies): void {
                if ($segment instanceof CUXCurrencyDetails) {
                    $values = $segment->rawValues();
                    // CUX format: CUX+<usage>:<currency>:<qualifier>
                    if (is_array($values[1] ?? null) && !empty($values[1][1] ?? '')) {
                        $currencies[] = $values[1][1];
                    }
                }
            });

        return array_values(array_unique($currencies));
    }

    /**
     * Calculate total monetary amount from MOA segments
     *
     * @param string|null $qualifier Filter by specific MOA qualifier (e.g., '79' = Total amount, '125' = Taxable amount)
     */
    public function calculateTotalAmount(?string $qualifier = null): float
    {
        $total = 0.0;

        $query = $this->message->query()->withTag('MOA');

        if ($qualifier !== null) {
            $query = $query->where(static function ($segment) use ($qualifier) {
                $values = $segment->rawValues()[1] ?? [];
                return is_array($values) && ($values[0] ?? '') === $qualifier;
            });
        }

        $query->each(static function ($segment) use (&$total): void {
            $values = $segment->rawValues()[1] ?? [];
            if (is_array($values)) {
                $total += (float) ($values[1] ?? 0);
            }
        });

        return $total;
    }

    /**
     * Calculate total quantity from QTY segments
     *
     * @param string|null $qualifier Filter by specific QTY qualifier (e.g., '21' = Ordered)
     */
    public function calculateTotalQuantity(?string $qualifier = null): float
    {
        $total = 0.0;

        $query = $this->message->query()->withTag('QTY');

        if ($qualifier !== null) {
            $query = $query->where(static function ($segment) use ($qualifier) {
                if ($segment instanceof QTYQuantity) {
                    return $segment->qualifier() === $qualifier;
                }
                return $segment->subId() === $qualifier;
            });
        }

        $query->each(static function ($segment) use (&$total): void {
            if ($segment instanceof QTYQuantity) {
                $total += $segment->quantityAsFloat();
            }
        });

        return $total;
    }

    /**
     * Get summary statistics as an array
     *
     * @return array<string, mixed>
     */
    public function getSummary(): array
    {
        return [
            'message_type' => $this->getType(),
            'total_segments' => $this->segmentCount(),
            'line_items' => $this->lineItemCount(),
            'addresses' => $this->addressCount(),
            'party_qualifiers' => $this->getPartyQualifiers(),
            'currencies' => $this->getCurrencies(),
            'segment_counts' => [
                'NAD' => $this->segmentCountByTag('NAD'),
                'LIN' => $this->segmentCountByTag('LIN'),
                'QTY' => $this->segmentCountByTag('QTY'),
                'PRI' => $this->segmentCountByTag('PRI'),
                'MOA' => $this->segmentCountByTag('MOA'),
                'DTM' => $this->segmentCountByTag('DTM'),
            ],
        ];
    }

    /**
     * Check if message has specific segment
     */
    public function hasSegment(string $tag): bool
    {
        return $this->message->query()->withTag($tag)->exists();
    }

    /**
     * Check if message has summary section (UNS segment)
     */
    public function hasSummarySection(): bool
    {
        return $this->hasSegment('UNS');
    }
}
