<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use function is_array;

/** @psalm-immutable */
final class UNEFunctionalGroupTrailer extends AbstractSegment
{
    public function tag(): string
    {
        return 'UNE';
    }

    /**
     * Number of messages in the functional group
     */
    public function controlCount(): string
    {
        $value = $this->rawValues()[1] ?? '';

        return is_array($value) ? '' : (string) $value;
    }

    /**
     * Functional group reference number (must match the UNG)
     */
    public function groupReference(): string
    {
        $value = $this->rawValues()[2] ?? '';

        return is_array($value) ? '' : (string) $value;
    }
}
