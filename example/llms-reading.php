<?php

declare(strict_types=1);

/**
 * Executable version of docs/llms/reading.md. Run by CI so the documentation cannot rot.
 */

use EdifactParser\Analysis\MessageAnalyzer;
use EdifactParser\ContextSegment;
use EdifactParser\Directory\GroupInstance;
use EdifactParser\Directory\StructureGrouper;
use EdifactParser\Directory\XmlDirectory;
use EdifactParser\EdifactParser;
use EdifactParser\Segments\NADNameAddress;

require __DIR__ . '/../vendor/autoload.php';

$edi = <<<'EDI'
    UNA:+.? '
    UNB+UNOC:3+SENDER+RECIPIENT+240101:1200+REF01'
    UNH+1+ORDERS:D:96A:UN'
    BGM+220+ORD1+9'
    DTM+137:20240101:102'
    NAD+BY+123::9+++Some Street 1+Berlin++10115+DE'
    CTA+IC+:John Doe'
    COM+john@example.com:EM'
    CUX+2:EUR:9'
    LIN+1++ART1:BP'
    QTY+21:100:PCE'
    PRI+AAA:12.50'
    UNS+S'
    MOA+79:1250'
    CNT+2:1'
    UNT+14+1'
    UNZ+1+REF01'
    EDI;

$result = EdifactParser::createWithDefaultSegments()->parse($edi);
$message = $result->transactionMessages()[0];

// --- Keyed lookups return the typed segment ---------------------------------
$buyer = $message->segmentByTagAndSubId('NAD', 'BY');
assert($buyer instanceof NADNameAddress);
assert($buyer->partyQualifier() === 'BY');
assert($buyer->city() === 'Berlin');
assert($buyer->countryCode() === 'DE');
assert($message->segmentsByTag('NAD') !== []);
assert($message->segmentByTagAndSubId('NAD', 'ZZ') === null);

// --- Typed accessors --------------------------------------------------------
$qty = $message->lineItemById(1)?->segmentByTagAndSubId('QTY', '21');
assert($qty !== null);
assert($qty->quantityAsFloat() === 100.0);
assert($qty->measureUnit() === 'PCE');

$pri = $message->lineItemById(1)?->segmentByTagAndSubId('PRI', 'AAA');
assert($pri !== null && $pri->priceAsFloat() === 12.5);

$dtm = $message->segmentByTagAndSubId('DTM', '137');
assert($dtm !== null && $dtm->asDateTime() !== null);

assert($buyer->tag() === 'NAD');
assert($buyer->subId() === 'BY');
assert($buyer->rawValues()[0] === 'NAD');
assert(array_key_exists('rawValues', $buyer->toArray()));

// --- Query API --------------------------------------------------------------
$query = $message->query();
assert(count($query->withTag('NAD')->get()) === 1);
assert(count($query->withTags(['NAD', 'LIN'])->get()) === 2);
assert(!in_array('UNH', array_map(static fn ($s) => $s->tag(), $query->withoutTags(['UNH', 'UNT'])->get()), true));
assert(count($query->ofType(NADNameAddress::class)->get()) === 1);
assert(count($query->where(static fn ($s) => $s->tag() === 'QTY')->get()) === 1);
assert($query->withTag('NAD')->first() !== null);
assert($query->withTag('NAD')->last() !== null);
assert($query->withTag('NAD')->count() === 1);
assert($query->withTag('UNS')->exists() === true);
assert($query->withTag('ZZZ')->isEmpty() === true);
assert(count($query->withTag('NAD')->limit(10)->skip(0)->get()) === 1);
assert($query->withTag('NAD')->map(static fn ($s) => $s->tag()) === ['NAD']);
assert($query->withTag('MOA')->reduce(static fn (float $t, $s) => $t + (float) $s->rawValues()[1][1], 0.0) === 1250.0);
assert(array_key_exists('NAD', $query->groupByTag()));
assert($query->countByTag()['NAD'] === 1);
$query->withTag('NAD')->each(static fn ($s) => null);
foreach ($query->withTag('NAD') as $nad) {
    assert($nad->tag() === 'NAD');
}

// --- Line items -------------------------------------------------------------
foreach ($message->lineItems() as $lineItem) {
    assert($lineItem->segmentByTagAndSubId('LIN', '1') !== null);
    assert(count($lineItem) > 0);
    foreach ($lineItem as $segment) {
        assert($segment->tag() !== '');
    }
}
assert($message->lineItemById(1) !== null);
assert($message->lineItemById(99) === null);

// --- Context segments -------------------------------------------------------
foreach ($message->contextSegments() as $context) {
    assert($context instanceof ContextSegment);
    assert($context->tag() !== '');
    $context->children();
    $context->childByTag('CTA');
    $context->childrenByTag('COM');
    $context->hasChildren();
    assert(count($context) === count($context->children()));
    foreach ($context as $child) {
        assert($child->tag() !== '');
    }
    assert(array_key_exists('tag', $context->toArray()));
}

assert(array_map(static fn ($c) => $c->tag(), $message->childrenOf($buyer)) === ['CTA', 'COM']);
assert($message->contextFor($buyer) instanceof ContextSegment);

// --- Envelope metadata ------------------------------------------------------
$unb = $result->globalSegments()->segmentByTagAndSubId('UNB', 'UNOC');
assert($unb !== null);
assert($unb->syntaxIdentifier() === 'UNOC');
assert($unb->senderIdentification() === 'SENDER');
assert($unb->recipientIdentification() === 'RECIPIENT');
assert($unb->preparationDate() === '240101');
assert($unb->interchangeControlReference() === 'REF01');

// --- Statistics -------------------------------------------------------------
$analyzer = new MessageAnalyzer($message);
assert($analyzer->getType() === 'ORDERS');
assert($analyzer->segmentCount() === 14);
assert($analyzer->segmentCountByTag('NAD') === 1);
assert($analyzer->lineItemCount() === 1);
assert($analyzer->getPartyQualifiers() === ['BY']);
assert($analyzer->getCurrencies() === ['EUR']);
assert($analyzer->calculateTotalAmount('79') === 1250.0);
assert($analyzer->calculateTotalQuantity('21') === 100.0);
assert($analyzer->hasSegment('UNS') === true);
assert(array_key_exists('segment_counts', $analyzer->getSummary()));

// --- Segment groups (directory-driven) --------------------------------------
$structure = XmlDirectory::locate('D96A')?->messageStructure('ORDERS');

if ($structure !== null) {
    assert($structure->messageType() === 'ORDERS');
    assert($structure->groupCount() > 40);
    assert($structure->group('SG2')?->triggerTag() === 'NAD');
    assert($structure->group('SG9999') === null);

    $nodes = (new StructureGrouper())->group($message, $structure);
    $groups = array_values(array_filter($nodes, static fn ($n) => $n instanceof GroupInstance));
    assert($groups !== []);

    $sg2 = null;
    foreach ($groups as $group) {
        if ($group->id() === 'SG2') {
            $sg2 = $group;
            break;
        }
    }
    assert($sg2 instanceof GroupInstance);
    assert($sg2->occurrence() === 0);
    assert($sg2->segmentByTag('NAD') !== null);
    assert($sg2->childrenOfGroup('SG5') !== []);
    assert(count($sg2) >= 1);
    assert($sg2->toArray()['group'] === 'SG2');
}

echo "docs/llms/reading.md OK\n";
