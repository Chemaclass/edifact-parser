<?php

declare(strict_types=1);

namespace EdifactParser\Validation;

use EdifactParser\TransactionMessage;

/**
 * Checks a {@see TransactionMessage} against a {@see MessageRuleSet} and returns
 * the violations found. It never throws — an empty list means the message conforms.
 */
final class MessageValidator
{
    /**
     * @return list<ValidationViolation>
     */
    public function validate(TransactionMessage $message, MessageRuleSet $rules): array
    {
        $violations = [];
        // One pass over the message, then every rule is a hash lookup.
        $counts = $message->countByTag();

        foreach ($rules->requiredTags() as $tag) {
            if (!isset($counts[$tag])) {
                $violations[] = new ValidationViolation($tag, 'required', "Missing required segment '{$tag}'");
            }
        }

        foreach ($rules->cardinality() as $tag => $bounds) {
            $count = $counts[$tag] ?? 0;

            if ($count < $bounds['min']) {
                $violations[] = new ValidationViolation(
                    $tag,
                    'cardinality',
                    "Segment '{$tag}' occurs {$count} time(s), expected at least {$bounds['min']}",
                );
            }

            if ($bounds['max'] !== null && $count > $bounds['max']) {
                $violations[] = new ValidationViolation(
                    $tag,
                    'cardinality',
                    "Segment '{$tag}' occurs {$count} time(s), expected at most {$bounds['max']}",
                );
            }
        }

        $sequenceViolation = $this->checkSequence($message, $rules);
        if ($sequenceViolation !== null) {
            $violations[] = $sequenceViolation;
        }

        return $violations;
    }

    public function isValid(TransactionMessage $message, MessageRuleSet $rules): bool
    {
        return $this->validate($message, $rules) === [];
    }

    private function checkSequence(TransactionMessage $message, MessageRuleSet $rules): ?ValidationViolation
    {
        $sequence = $rules->sequence();
        if ($sequence === []) {
            return null;
        }

        $rank = array_flip($sequence);
        $highest = -1;

        foreach ($message as $segment) {
            $tag = $segment->tag();
            if (!isset($rank[$tag])) {
                continue;
            }

            if ($rank[$tag] < $highest) {
                return new ValidationViolation(
                    $tag,
                    'sequence',
                    'Segments are not in the expected order: ' . implode(' -> ', $sequence),
                );
            }

            $highest = $rank[$tag];
        }

        return null;
    }
}
