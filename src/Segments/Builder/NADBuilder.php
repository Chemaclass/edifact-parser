<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Builder;

use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\Qualifier\NADQualifier;

/**
 * Fluent builder for NAD (Name and Address) segments
 */
final class NADBuilder
{
    private string $qualifier = '';
    private array $partyIdentification = [];
    private string $name = '';
    private string $street = '';
    private string $city = '';
    private string $postalCode = '';
    private string $countryCode = '';

    public function withQualifier(string|NADQualifier $qualifier): self
    {
        $this->qualifier = $qualifier instanceof NADQualifier ? $qualifier->value : $qualifier;
        return $this;
    }

    /**
     * @param array<int, string> $identification [id, code list qualifier, code list agency]
     */
    public function withPartyIdentification(array $identification): self
    {
        $this->partyIdentification = $identification;
        return $this;
    }

    public function withPartyId(string $id, string $codeListQualifier = '', string $codeListAgency = ''): self
    {
        $this->partyIdentification = array_filter([$id, $codeListQualifier, $codeListAgency]);
        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function withStreet(string $street): self
    {
        $this->street = $street;
        return $this;
    }

    public function withCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function withPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function withCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function build(): NADNameAddress
    {
        $rawValues = [
            'NAD',
            $this->qualifier,
            $this->partyIdentification,
            '',
            $this->name,
            $this->street,
            $this->city,
            '',
            $this->postalCode,
            $this->countryCode,
        ];

        return new NADNameAddress($rawValues);
    }
}
