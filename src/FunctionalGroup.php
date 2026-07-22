<?php

declare(strict_types=1);

namespace EdifactParser;

use EdifactParser\Segments\UNEFunctionalGroupTrailer;
use EdifactParser\Segments\UNGFunctionalGroupHeader;

/**
 * A UNG...UNE functional group: a set of messages of the same type wrapped by a
 * UNG header and UNE trailer. Optional in EDIFACT — many interchanges send
 * messages directly under the interchange (UNB) with no functional group.
 */
final class FunctionalGroup
{
    /**
     * @param list<TransactionMessage> $messages
     */
    public function __construct(
        private UNGFunctionalGroupHeader $header,
        private array $messages,
        private ?UNEFunctionalGroupTrailer $trailer = null,
    ) {
    }

    public function header(): UNGFunctionalGroupHeader
    {
        return $this->header;
    }

    public function trailer(): ?UNEFunctionalGroupTrailer
    {
        return $this->trailer;
    }

    public function messageType(): string
    {
        return $this->header->messageType();
    }

    /**
     * @return list<TransactionMessage>
     */
    public function messages(): array
    {
        return $this->messages;
    }
}
