<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use function is_array;

/** @psalm-immutable */
final class NADNameAddress extends AbstractSegment
{
    public function tag(): string
    {
        return 'NAD';
    }

    /**
     * Party qualifier (e.g., 'BY' = Buyer, 'SU' = Supplier, 'CN' = Consignee, 'CZ' = Consignor)
     */
    public function partyQualifier(): string
    {
        return $this->rawValues()[1] ?? '';
    }

    /**
     * Party identification (array format: [id, code list qualifier, code list agency])
     *
     * @return array<int, string>
     */
    public function partyIdentification(): array
    {
        $value = $this->rawValues()[2] ?? [];
        return is_array($value) ? $value : [];
    }

    /**
     * Party ID (first element of party identification)
     */
    public function partyId(): string
    {
        return $this->partyIdentification()[0] ?? '';
    }

    /**
     * Party name
     */
    public function name(): string
    {
        return $this->rawValues()[4] ?? '';
    }

    /**
     * Street and number
     */
    public function street(): string
    {
        return $this->rawValues()[5] ?? '';
    }

    /**
     * City name
     */
    public function city(): string
    {
        return $this->rawValues()[6] ?? '';
    }

    /**
     * Postal code
     */
    public function postalCode(): string
    {
        return $this->rawValues()[8] ?? '';
    }

    /**
     * Country code (ISO 3166-1 alpha-2)
     */
    public function countryCode(): string
    {
        return $this->rawValues()[9] ?? '';
    }
}
