<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class TAXDutyTaxFee extends AbstractSegment
{
    public function tag(): string
    {
        return 'TAX';
    }

    /**
     * Duty/tax/fee function qualifier (5283), e.g. '7' = tax.
     */
    public function functionQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * Duty/tax/fee type code (C241/5153): TAX+7+VAT+++:::19+S -> 'VAT'.
     */
    public function typeCode(): string
    {
        return $this->firstComponent(2);
    }

    /**
     * Duty/tax/fee rate (C243/5278): TAX+7+VAT+++:::19+S -> '19'.
     */
    public function rate(): string
    {
        return $this->component(3, 5);
    }

    /**
     * Duty/tax/fee category code (5305), e.g. 'S' = standard rate,
     * 'Z' = zero rated.
     */
    public function categoryCode(): string
    {
        return $this->element(6);
    }
}
