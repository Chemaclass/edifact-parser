<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Qualifier;

/**
 * NAD (Name and Address) party qualifiers
 *
 * Common qualifiers used to identify the role of a party in the transaction
 */
final class NADQualifier
{
    /** Buyer */
    public const BUYER = 'BY';

    /** Supplier */
    public const SUPPLIER = 'SU';

    /** Consignee (delivery party) */
    public const CONSIGNEE = 'CN';

    /** Consignor (sender/shipper) */
    public const CONSIGNOR = 'CZ';

    /** Delivery party */
    public const DELIVERY_PARTY = 'DP';

    /** Invoicee */
    public const INVOICEE = 'IV';

    /** Payer */
    public const PAYER = 'PR';

    /** Carrier */
    public const CARRIER = 'CA';

    /** Freight forwarder */
    public const FREIGHT_FORWARDER = 'FW';

    /** Manufacturer */
    public const MANUFACTURER = 'MF';

    /** Ultimate consignee */
    public const ULTIMATE_CONSIGNEE = 'UC';

    /** Warehouse keeper */
    public const WAREHOUSE_KEEPER = 'WH';

    private function __construct()
    {
        // Prevent instantiation
    }
}
