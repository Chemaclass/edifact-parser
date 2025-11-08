<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Qualifier;

/**
 * PRI (Price) qualifiers
 *
 * Identifies the type of price
 */
final class PRIQualifier
{
    /** Calculation net */
    public const CALCULATION_NET = 'AAA';

    /** Calculation gross */
    public const CALCULATION_GROSS = 'AAB';

    /** Information price, excluding allowances or charges */
    public const INFORMATION_PRICE = 'AAE';

    /** Gross price */
    public const GROSS = 'AAF';

    /** Net price */
    public const NET = 'AAG';

    /** Catalogue price */
    public const CATALOGUE = 'CAL';

    /** Contract price */
    public const CONTRACT = 'CT';

    /** Discount price */
    public const DISCOUNT = 'DIS';

    /** List price */
    public const LIST = 'LIS';

    /** Minimum order price */
    public const MINIMUM_ORDER = 'MIN';

    /** Recommended retail price */
    public const RECOMMENDED_RETAIL = 'RRP';

    private function __construct()
    {
        // Prevent instantiation
    }
}
