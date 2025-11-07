<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

use EdifactParser\Exception\MissingSubId;

use function is_array;

/** @psalm-immutable */
final class UNHMessageHeader extends AbstractSegment
{
    public function tag(): string
    {
        return 'UNH';
    }

    public function subId(): string
    {
        if (!isset($this->rawValues[1][0])) {
            throw new MissingSubId('[1][0]', $this->rawValues);
        }

        return (string) $this->rawValues[1][0];
    }

    /**
     * Message reference number
     */
    public function messageReferenceNumber(): string
    {
        return $this->rawValues()[1] ?? '';
    }

    /**
     * Message identifier array (e.g., ['IFTMIN', 'S', '93A', 'UN', 'PN001'])
     *
     * @return array<int, string>
     */
    public function messageIdentifier(): array
    {
        $value = $this->rawValues()[2] ?? [];
        return is_array($value) ? $value : [];
    }

    /**
     * Message type (e.g., 'ORDERS', 'INVOIC', 'IFTMIN', 'DESADV')
     *
     * Common types:
     * - ORDERS: Purchase order
     * - INVOIC: Invoice
     * - DESADV: Despatch advice
     * - IFTMIN: Instruction for forwarding and transport
     * - ORDRSP: Purchase order response
     */
    public function messageType(): string
    {
        return $this->messageIdentifier()[0] ?? '';
    }

    /**
     * Message version number (e.g., 'D', 'S')
     */
    public function messageVersionNumber(): string
    {
        return $this->messageIdentifier()[1] ?? '';
    }

    /**
     * Message release number (e.g., '96A', '93A')
     */
    public function messageReleaseNumber(): string
    {
        return $this->messageIdentifier()[2] ?? '';
    }

    /**
     * Controlling agency (e.g., 'UN' = UN/CEFACT)
     */
    public function controllingAgency(): string
    {
        return $this->messageIdentifier()[3] ?? '';
    }
}
