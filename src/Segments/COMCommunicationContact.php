<?php

declare(strict_types=1);

namespace EdifactParser\Segments;

/** @psalm-immutable */
final class COMCommunicationContact extends AbstractSegment
{
    public function tag(): string
    {
        return 'COM';
    }

    /**
     * Communication number or address (C076/3148):
     * COM+john@acme.com:EM -> 'john@acme.com'.
     */
    public function communicationNumber(): string
    {
        return $this->component(0);
    }

    /**
     * Communication channel qualifier (C076/3155), e.g. 'EM' = email,
     * 'TE' = telephone, 'FX' = fax.
     */
    public function channelQualifier(): string
    {
        return $this->component(1);
    }
}
