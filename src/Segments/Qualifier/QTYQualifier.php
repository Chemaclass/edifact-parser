<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Qualifier;

/**
 * QTY (Quantity) qualifiers
 *
 * Defines the type of quantity being specified
 */
enum QTYQualifier: string
{
    /** Discrete quantity */
    case DISCRETE = '1';

    /** Cumulative quantity */
    case CUMULATIVE = '3';

    /** Number of consumer units in the traded unit */
    case CONSUMER_UNITS = '11';

    /** Dispatched quantity */
    case DISPATCHED = '12';

    /** Ordered quantity */
    case ORDERED = '21';

    /** Quantity on hand */
    case ON_HAND = '33';

    /** Received quantity */
    case RECEIVED = '48';

    /** Invoiced quantity */
    case INVOICED = '47';

    /** Quantity to be delivered */
    case TO_BE_DELIVERED = '46';

    /** Free goods quantity */
    case FREE_GOODS = '192';
}
