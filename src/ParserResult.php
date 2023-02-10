<?php

declare(strict_types=1);

namespace EdifactParser;

final class ParserResult
{
    /**
     * @param list<TransactionMessage> $transactionMessages
     */
    public function __construct(
        private TransactionMessage $globalSegments,
        private array $transactionMessages,
    ) {
    }

    public function globalSegments(): TransactionMessage
    {
        return $this->globalSegments;
    }

    public function transactionMessages(): array
    {
        return $this->transactionMessages;
    }
}
