<?php

declare(strict_types=1);

namespace EdifactParser\Analysis;

use EdifactParser\Segments\CUXCurrencyDetails;
use EdifactParser\Segments\MOAMonetaryAmount;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;
use EdifactParser\Segments\SegmentInterface;
use EdifactParser\TransactionMessage;

use function count;
use function is_array;

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

    public function segmentCount(): int
    {
        return $this->message->query()->count();
    }

    public function segmentCountByTag(string $tag): int
    {
        return $this->message->query()->withTag($tag)->count();
    }

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
        $qualifiers = $this->message->query()
            ->withTag('NAD')
            ->map(static function (SegmentInterface $segment) {
                if ($segment instanceof NADNameAddress) {
                    return $segment->partyQualifier();
                }
                return $segment->rawValues()[1] ?? '';
            });

        return array_values(array_unique($qualifiers));
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
            ->each(static function (SegmentInterface $segment) use (&$currencies): void {
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
            $query = $query->where(static fn (SegmentInterface $s) => self::moaQualifier($s) === $qualifier);
        }

        $query->each(static function (SegmentInterface $s) use (&$total): void {
            $total += self::moaAmount($s);
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
            $query = $query->where(static function (SegmentInterface $segment) use ($qualifier): bool {
                if ($segment instanceof QTYQuantity) {
                    return $segment->qualifier() === $qualifier;
                }
                return $segment->subId() === $qualifier;
            });
        }

        $query->each(static function (SegmentInterface $segment) use (&$total): void {
            if ($segment instanceof QTYQuantity) {
                $total += $segment->quantityAsFloat();
            }
        });

        return $total;
    }

    /**
     * @return array{
     *     message_type: string|null,
     *     total_segments: int,
     *     line_items: int,
     *     addresses: int,
     *     party_qualifiers: list<string>,
     *     currencies: list<string>,
     *     segment_counts: array{NAD: int, LIN: int, QTY: int, PRI: int, MOA: int, DTM: int}
     * }
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

    private static function moaQualifier(SegmentInterface $segment): string
    {
        if ($segment instanceof MOAMonetaryAmount) {
            return $segment->amountQualifier();
        }

        $values = $segment->rawValues()[1] ?? [];

        return is_array($values) ? (string) ($values[0] ?? '') : '';
    }

    private static function moaAmount(SegmentInterface $segment): float
    {
        if ($segment instanceof MOAMonetaryAmount) {
            return $segment->amountAsFloat();
        }

        $values = $segment->rawValues()[1] ?? [];

        return is_array($values) ? (float) ($values[1] ?? 0) : 0.0;
    }
}
