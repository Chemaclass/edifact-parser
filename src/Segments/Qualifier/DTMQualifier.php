<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Qualifier;

/**
 * DTM (Date/Time/Period) qualifiers
 *
 * Identifies the type of date being specified
 */
enum DTMQualifier: string
{
    /** Document date */
    case DOCUMENT_DATE = '137';

    /** Delivery date/time, requested */
    case DELIVERY_REQUESTED = '2';

    /** Invoice date */
    case INVOICE_DATE = '3';

    /** Order date */
    case ORDER_DATE = '4';

    /** Delivery date/time, actual */
    case DELIVERY_ACTUAL = '35';

    /** Shipment date/time, requested */
    case SHIPMENT_REQUESTED = '10';

    /** Despatch date */
    case DESPATCH_DATE = '11';

    /** Due date */
    case DUE_DATE = '13';

    /** Expiry date */
    case EXPIRY_DATE = '36';

    /** Processing date */
    case PROCESSING_DATE = '63';

    /** Tax period start */
    case TAX_PERIOD_START = '131';

    /** Tax period end */
    case TAX_PERIOD_END = '132';

    /** Validity period start */
    case VALIDITY_START = '157';

    /** Validity period end */
    case VALIDITY_END = '158';
}
