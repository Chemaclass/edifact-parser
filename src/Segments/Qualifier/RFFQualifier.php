<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Qualifier;

/**
 * RFF (Reference) qualifiers
 *
 * Identifies the type of reference number
 */
final class RFFQualifier
{
    /** Order number */
    public const ORDER_NUMBER = 'ON';

    /** Invoice number */
    public const INVOICE_NUMBER = 'IV';

    /** Delivery note number */
    public const DELIVERY_NOTE = 'DQ';

    /** Customs reference */
    public const CUSTOMS_REFERENCE = 'CU';

    /** Shipment reference number */
    public const SHIPMENT_REFERENCE = 'SRN';

    /** Contract number */
    public const CONTRACT_NUMBER = 'CT';

    /** Purchase order response number */
    public const PO_RESPONSE_NUMBER = 'POR';

    /** Proforma invoice number */
    public const PROFORMA_INVOICE = 'PI';

    /** Consignment identifier */
    public const CONSIGNMENT_ID = 'CN';

    /** Packing list number */
    public const PACKING_LIST = 'PK';

    /** Buyer's item number */
    public const BUYER_ITEM_NUMBER = 'BO';

    /** Supplier's item number */
    public const SUPPLIER_ITEM_NUMBER = 'SA';

    /** Price list number */
    public const PRICE_LIST_NUMBER = 'PL';

    /** Reference version number */
    public const REFERENCE_VERSION = 'VN';

    private function __construct()
    {
        // Prevent instantiation
    }
}
