<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class TDTTransportDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'TDT';
    }

    /**
     * Transport stage qualifier (e.g., '20' = Main-carriage transport, '30' = On-carriage)
     */
    public function transportStageQualifier(): string
    {
        return $this->element(1);
    }

    /**
     * Means of transport journey / conveyance reference number
     */
    public function conveyanceReference(): string
    {
        return $this->element(2);
    }

    /**
     * Mode of transport code (e.g., '10' = Maritime, '20' = Rail, '30' = Road, '40' = Air)
     */
    public function modeOfTransport(): string
    {
        return $this->firstComponent(3);
    }

    /**
     * Carrier identification
     */
    public function carrierId(): string
    {
        return $this->firstComponent(5);
    }
}
