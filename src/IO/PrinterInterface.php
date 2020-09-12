<?php

declare(strict_types=1);

namespace EdifactParser\IO;

use EdifactParser\TransactionMessage;

interface PrinterInterface
{
    public function printMessage(TransactionMessage $message): void;
}
