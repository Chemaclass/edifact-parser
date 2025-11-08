<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Qualifier;

/**
 * NAD (Name and Address) party qualifiers
 *
 * Common qualifiers used to identify the role of a party in the transaction
 */
enum NADQualifier: string
{
    /** Buyer */
    case BUYER = 'BY';

    /** Supplier */
    case SUPPLIER = 'SU';

    /** Consignee (delivery party) */
    case CONSIGNEE = 'CN';

    /** Consignor (sender/shipper) */
    case CONSIGNOR = 'CZ';

    /** Delivery party */
    case DELIVERY_PARTY = 'DP';

    /** Invoicee */
    case INVOICEE = 'IV';

    /** Payer */
    case PAYER = 'PR';

    /** Carrier */
    case CARRIER = 'CA';

    /** Freight forwarder */
    case FREIGHT_FORWARDER = 'FW';

    /** Manufacturer */
    case MANUFACTURER = 'MF';

    /** Ultimate consignee */
    case ULTIMATE_CONSIGNEE = 'UC';

    /** Warehouse keeper */
    case WAREHOUSE_KEEPER = 'WH';
}
