<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Qualifier;

/**
 * PRI (Price) qualifiers
 *
 * Identifies the type of price
 */
enum PRIQualifier: string
{
    /** Calculation net */
    case CALCULATION_NET = 'AAA';

    /** Calculation gross */
    case CALCULATION_GROSS = 'AAB';

    /** Information price, excluding allowances or charges */
    case INFORMATION_PRICE = 'AAE';

    /** Gross price */
    case GROSS = 'AAF';

    /** Net price */
    case NET = 'AAG';

    /** Catalogue price */
    case CATALOGUE = 'CAL';

    /** Contract price */
    case CONTRACT = 'CT';

    /** Discount price */
    case DISCOUNT = 'DIS';

    /** List price */
    case LIST = 'LIS';

    /** Minimum order price */
    case MINIMUM_ORDER = 'MIN';

    /** Recommended retail price */
    case RECOMMENDED_RETAIL = 'RRP';
}
