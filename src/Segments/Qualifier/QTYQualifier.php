<?php

declare(strict_types=1);

namespace EdifactParser\Segments\Qualifier;

/**
 * QTY (Quantity) qualifiers
 *
 * Defines the type of quantity being specified
 */
final class QTYQualifier
{
    /** Discrete quantity */
    public const DISCRETE = '1';

    /** Cumulative quantity */
    public const CUMULATIVE = '3';

    /** Number of consumer units in the traded unit */
    public const CONSUMER_UNITS = '11';

    /** Dispatched quantity */
    public const DISPATCHED = '12';

    /** Ordered quantity */
    public const ORDERED = '21';

    /** Quantity on hand */
    public const ON_HAND = '33';

    /** Received quantity */
    public const RECEIVED = '48';

    /** Invoiced quantity */
    public const INVOICED = '47';

    /** Quantity to be delivered */
    public const TO_BE_DELIVERED = '46';

    /** Free goods quantity */
    public const FREE_GOODS = '192';

    private function __construct()
    {
        // Prevent instantiation
    }
}
