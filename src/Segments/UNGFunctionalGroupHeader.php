<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use function is_array;

/** @psalm-immutable */
final class UNGFunctionalGroupHeader extends AbstractSegment
{
    public function tag(): string
    {
        return 'UNG';
    }

    /**
     * Message type grouped by this functional group (e.g., 'ORDERS', 'INVOIC')
     */
    public function messageType(): string
    {
        $value = $this->rawValues()[1] ?? '';

        return is_array($value) ? '' : (string) $value;
    }

    /**
     * Functional group reference number
     */
    public function groupReference(): string
    {
        $value = $this->rawValues()[5] ?? '';

        return is_array($value) ? '' : (string) $value;
    }
}
