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
    /**
     * @codeCoverageIgnore Prevents instantiation of this constants/utility holder
     */
    private function __construct()
    {
    }

    /**
     * Purchase order.
     */
    public static function orders(): MessageRuleSet
    {
        return self::serviceEnvelope('ORDERS')
            ->inSequence('UNH', 'BGM', 'DTM', 'NAD', 'LIN', 'UNS', 'CNT', 'UNT');
    }

    /**
     * Invoice.
     */
    public static function invoic(): MessageRuleSet
    {
        return self::serviceEnvelope('INVOIC')
            ->inSequence('UNH', 'BGM', 'DTM', 'NAD', 'CUX', 'LIN', 'UNS', 'MOA', 'UNT');
    }

    /**
     * Despatch advice.
     */
    public static function desadv(): MessageRuleSet
    {
        return self::serviceEnvelope('DESADV')
            ->inSequence('UNH', 'BGM', 'DTM', 'NAD', 'CPS', 'LIN', 'UNT');
    }

    /**
     * Transport instruction (forwarding and transport).
     */
    public static function iftmin(): MessageRuleSet
    {
        return self::serviceEnvelope('IFTMIN')
            ->inSequence('UNH', 'BGM', 'DTM', 'NAD', 'UNT');
    }

    /**
     * The mandatory service segments (UNH/BGM/UNT, exactly one each) shared by every
     * message type; callers append the type-specific sequence.
     */
    private static function serviceEnvelope(string $type): MessageRuleSet
    {
        return MessageRuleSet::forType($type)
            ->require('UNH', 'BGM', 'UNT')
            ->occurs('BGM', 1, 1)
            ->occurs('UNH', 1, 1)
            ->occurs('UNT', 1, 1);
    }
}
