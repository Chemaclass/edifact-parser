<?php

declare(strict_types=1);

namespace EdifactParser\Validation;

/**
 * Ready-to-use {@see MessageRuleSet}s for common UN/EDIFACT message types. These are
 * pragmatic starting points (mandatory service segments + typical order), not full
 * D.xx specifications — extend them with `->require()`, `->occurs()`, `->inSequence()`
 * for your trading-partner requirements.
 */
final class MessageRuleSets
{
    private function __construct()
    {
    }

    /**
     * Purchase order.
     */
    public static function orders(): MessageRuleSet
    {
        return MessageRuleSet::forType('ORDERS')
            ->require('UNH', 'BGM', 'UNT')
            ->occurs('BGM', 1, 1)
            ->occurs('UNH', 1, 1)
            ->occurs('UNT', 1, 1)
            ->inSequence('UNH', 'BGM', 'DTM', 'NAD', 'LIN', 'UNS', 'CNT', 'UNT');
    }

    /**
     * Invoice.
     */
    public static function invoic(): MessageRuleSet
    {
        return MessageRuleSet::forType('INVOIC')
            ->require('UNH', 'BGM', 'UNT')
            ->occurs('BGM', 1, 1)
            ->occurs('UNH', 1, 1)
            ->occurs('UNT', 1, 1)
            ->inSequence('UNH', 'BGM', 'DTM', 'NAD', 'CUX', 'LIN', 'UNS', 'MOA', 'UNT');
    }

    /**
     * Despatch advice.
     */
    public static function desadv(): MessageRuleSet
    {
        return MessageRuleSet::forType('DESADV')
            ->require('UNH', 'BGM', 'UNT')
            ->occurs('BGM', 1, 1)
            ->occurs('UNH', 1, 1)
            ->occurs('UNT', 1, 1)
            ->inSequence('UNH', 'BGM', 'DTM', 'NAD', 'CPS', 'LIN', 'UNT');
    }

    /**
     * Transport instruction (forwarding and transport).
     */
    public static function iftmin(): MessageRuleSet
    {
        return MessageRuleSet::forType('IFTMIN')
            ->require('UNH', 'BGM', 'UNT')
            ->occurs('BGM', 1, 1)
            ->occurs('UNH', 1, 1)
            ->occurs('UNT', 1, 1)
            ->inSequence('UNH', 'BGM', 'DTM', 'NAD', 'UNT');
    }
}
