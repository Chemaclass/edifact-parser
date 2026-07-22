<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use DateTimeImmutable;

/** @psalm-immutable */
final class DTMDateTimePeriod extends AbstractSegment
{
    public function tag(): string
    {
        return 'DTM';
    }

    public function subId(): string
    {
        return $this->requiredSubId();
    }

    /**
     * Date/time/period qualifier (e.g., '10' = Shipment date, '137' = Document date)
     */
    public function qualifier(): string
    {
        return $this->component(0);
    }

    public function dateTime(): string
    {
        return $this->component(1);
    }

    /**
     * Date/time/period format qualifier (e.g., '102' = CCYYMMDD, '203' = CCYYMMDDHHMM)
     */
    public function formatQualifier(): string
    {
        return $this->component(2);
    }

    /**
     * Parse date/time as DateTimeImmutable
     * Supports common EDIFACT formats: 102 (CCYYMMDD), 203 (CCYYMMDDHHMM)
     *
     * @return DateTimeImmutable|null Returns null if parsing fails
     */
    public function asDateTime(): ?DateTimeImmutable
    {
        $dateTimeStr = $this->dateTime();
        if (empty($dateTimeStr)) {
            return null;
        }

        $format = match ($this->formatQualifier()) {
            '102' => 'Ymd',           // CCYYMMDD
            '203' => 'YmdHi',         // CCYYMMDDHHMM
            '204' => 'YmdHis',        // CCYYMMDDHHMMSS
            default => null,
        };

        if ($format === null) {
            return null;
        }

        $parsed = DateTimeImmutable::createFromFormat($format, $dateTimeStr);
        return $parsed !== false ? $parsed : null;
    }
}
