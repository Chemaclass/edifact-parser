<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use DateTimeImmutable;
use EdifactParser\Exception\MissingSubId;

use function is_array;

/** @psalm-immutable */
final class DTMDateTimePeriod extends AbstractSegment
{
    public function tag(): string
    {
        return 'DTM';
    }

    public function subId(): string
    {
        if (!isset($this->rawValues[1][0])) {
            throw new MissingSubId('[1][0]', $this->rawValues);
        }

        return (string) $this->rawValues[1][0];
    }

    /**
     * Date/time/period qualifier (e.g., '10' = Shipment date, '137' = Document date)
     */
    public function qualifier(): string
    {
        $value = $this->rawValues()[1] ?? [];
        return is_array($value) ? ($value[0] ?? '') : '';
    }

    /**
     * Date/time/period value (raw string)
     */
    public function dateTime(): string
    {
        $value = $this->rawValues()[1] ?? [];
        return is_array($value) ? ($value[1] ?? '') : '';
    }

    /**
     * Date/time/period format qualifier (e.g., '102' = CCYYMMDD, '203' = CCYYMMDDHHMM)
     */
    public function formatQualifier(): string
    {
        $value = $this->rawValues()[1] ?? [];
        return is_array($value) ? ($value[2] ?? '') : '';
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
