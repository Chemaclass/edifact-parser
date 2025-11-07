<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use function is_array;

/** @psalm-immutable */
final class RFFReference extends AbstractSegment
{
    public function tag(): string
    {
        return 'RFF';
    }

    public function subId(): string
    {
        return $this->rawValues[1][0];
    }

    /**
     * Reference qualifier (e.g., 'ON' = Order number, 'IV' = Invoice number, 'CU' = Customs reference)
     */
    public function qualifier(): string
    {
        $value = $this->rawValues()[1] ?? [];
        return is_array($value) ? ($value[0] ?? '') : '';
    }

    /**
     * Reference number/identifier
     */
    public function referenceNumber(): string
    {
        $value = $this->rawValues()[1] ?? [];
        return is_array($value) ? ($value[1] ?? '') : '';
    }
}
