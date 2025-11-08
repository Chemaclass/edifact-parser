<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Qualifier;

/**
 * RFF (Reference) qualifiers
 *
 * Identifies the type of reference number
 */
enum RFFQualifier: string
{
    /** Order number */
    case ORDER_NUMBER = 'ON';

    /** Invoice number */
    case INVOICE_NUMBER = 'IV';

    /** Delivery note number */
    case DELIVERY_NOTE = 'DQ';

    /** Customs reference */
    case CUSTOMS_REFERENCE = 'CU';

    /** Shipment reference number */
    case SHIPMENT_REFERENCE = 'SRN';

    /** Contract number */
    case CONTRACT_NUMBER = 'CT';

    /** Purchase order response number */
    case PO_RESPONSE_NUMBER = 'POR';

    /** Proforma invoice number */
    case PROFORMA_INVOICE = 'PI';

    /** Consignment identifier */
    case CONSIGNMENT_ID = 'CN';

    /** Packing list number */
    case PACKING_LIST = 'PK';

    /** Buyer's item number */
    case BUYER_ITEM_NUMBER = 'BO';

    /** Supplier's item number */
    case SUPPLIER_ITEM_NUMBER = 'SA';

    /** Price list number */
    case PRICE_LIST_NUMBER = 'PL';

    /** Reference version number */
    case REFERENCE_VERSION = 'VN';
}
