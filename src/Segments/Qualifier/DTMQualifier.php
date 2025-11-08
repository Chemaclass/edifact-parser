<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Qualifier;

/**
 * DTM (Date/Time/Period) qualifiers
 *
 * Identifies the type of date being specified
 */
final class DTMQualifier
{
    /** Document date */
    public const DOCUMENT_DATE = '137';

    /** Delivery date/time, requested */
    public const DELIVERY_REQUESTED = '2';

    /** Invoice date */
    public const INVOICE_DATE = '3';

    /** Order date */
    public const ORDER_DATE = '4';

    /** Delivery date/time, actual */
    public const DELIVERY_ACTUAL = '35';

    /** Shipment date/time, requested */
    public const SHIPMENT_REQUESTED = '10';

    /** Despatch date */
    public const DESPATCH_DATE = '11';

    /** Due date */
    public const DUE_DATE = '13';

    /** Expiry date */
    public const EXPIRY_DATE = '36';

    /** Processing date */
    public const PROCESSING_DATE = '63';

    /** Tax period start */
    public const TAX_PERIOD_START = '131';

    /** Tax period end */
    public const TAX_PERIOD_END = '132';

    /** Validity period start */
    public const VALIDITY_START = '157';

    /** Validity period end */
    public const VALIDITY_END = '158';

    private function __construct()
    {
        // Prevent instantiation
    }
}
