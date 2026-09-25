<?php

declare(strict_types=1);

use EdifactParser\EdifactParser;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\QTYQuantity;

require __DIR__ . '/../vendor/autoload.php';

$edi = <<<'EDI'
    UNB+UNOC:3+SENDER+RECEIVER+240101:1200+1'
    UNH+1+ORDERS:D:96A:UN'
    BGM+220+PO-123+9'
    NAD+BY+123456++ACME Corporation+Street 1+Berlin++10115+DE'
    LIN+1++SKU-1:BP'
    QTY+21:100:PCE'
    UNT+6+1'
    UNZ+1+1'
    EDI;

$message = EdifactParser::createWithDefaultSegments()->parse($edi)->firstMessage();
$buyer = $message?->segmentOfType(NADNameAddress::class, 'BY');
$quantity = $message?->lineItemById(1)?->segmentOfType(QTYQuantity::class, '21');

ob_start();
printf("%s: %s, %.0f items\n", $message?->messageType(), $buyer?->name(), $quantity?->quantityAsFloat());
$output = ob_get_clean();

\assert($output === "ORDERS: ACME Corporation, 100 items\n");
echo $output;
