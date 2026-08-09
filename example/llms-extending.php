<?php

declare(strict_types=1);

/**
 * Executable version of docs/llms/extending.md. Run by CI so the documentation cannot rot.
 */

use EdifactParser\EdifactParser;
use EdifactParser\GroupingRules;
use EdifactParser\Segments\AbstractSegment;
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\SegmentFactory;
use EdifactParser\Tokenizer\TokenizerInterface;

require __DIR__ . '/../vendor/autoload.php';

/** @psalm-immutable */
final class EQDEquipmentDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'EQD';
    }

    public function equipmentTypeCode(): string
    {
        return $this->element(1);
    }

    public function identification(): string
    {
        return $this->firstComponent(2);
    }

    public function sizeType(): string
    {
        return $this->component(0, 3);
    }
}

// --- Custom segments --------------------------------------------------------
$factory = SegmentFactory::withAdditionalSegments(['EQD' => EQDEquipmentDetails::class]);
$parser = new EdifactParser($factory);

$message = $parser
    ->parse("UNH+1+IFTMIN:S:93A:UN'EQD+CN+ABCU1234567+4510:102:5'UNT+3+1'")
    ->transactionMessages()[0];

$eqd = $message->segmentByTagAndSubId('EQD', 'CN');
assert($eqd instanceof EQDEquipmentDetails);
assert($eqd->equipmentTypeCode() === 'CN');
assert($eqd->identification() === 'ABCU1234567');
assert($eqd->sizeType() === '4510');

// Defaults are still registered alongside the custom tag.
assert($factory->classForTag('NAD') === NADNameAddress::class);

$closed = SegmentFactory::withSegments(SegmentFactory::ENVELOPE_SEGMENTS + ['NAD' => NADNameAddress::class]);
assert($closed->classForTag('NAD') === NADNameAddress::class);
assert($closed->classForTag('QTY') === null);
assert(count(SegmentFactory::ENVELOPE_SEGMENTS) === 7);
assert(count(SegmentFactory::BUSINESS_SEGMENTS) === 25);

// --- Grouping rules ---------------------------------------------------------
$rules = GroupingRules::default()
    ->withContextTags(['NAD', 'LIN'])
    ->withChildTags(['CTA', 'COM', 'DTM'])
    ->withBreakLineItemTags(['UNS', 'CNT', 'UNT']);

assert($rules->contextTags() === ['NAD', 'LIN']);
assert($rules->childTags() === ['CTA', 'COM', 'DTM']);
assert($rules->breakLineItemTags() === ['UNS', 'CNT', 'UNT']);
assert($rules->isContextTag('NAD') === true);
assert($rules->isContextTag('DOC') === false);
assert(GroupingRules::DEFAULT_CONTEXT_TAGS === ['NAD', 'LIN', 'DOC']);
assert(GroupingRules::DEFAULT_CHILD_TAGS !== []);
assert(GroupingRules::DEFAULT_BREAK_LINE_ITEM_TAGS !== []);

$scoped = EdifactParser::createWithDefaultSegments($rules);
assert($scoped->parse("UNH+1+ORDERS:D:96A:UN'NAD+BY'CTA+IC'UNT+4+1'")->count() === 1);

// --- Custom tokenizer -------------------------------------------------------
$tokenizer = new class() implements TokenizerInterface {
    public function tokenize(string $content): array
    {
        return [['UNH', '1', ['ORDERS', 'D', '96A', 'UN']], ['UNT', '2', '1']];
    }
};

$fixed = new EdifactParser(SegmentFactory::withDefaultSegments(), tokenizer: $tokenizer);
assert($fixed->parse('ignored')->transactionMessages()[0]->messageType() === 'ORDERS');

// --- Introspection ----------------------------------------------------------
$defaults = SegmentFactory::withDefaultSegments();
assert(count($defaults->registeredTags()) === 32);
assert($defaults->classForTag('NAD') === NADNameAddress::class);
assert($defaults->classForTag('ZZZ') === null);
assert(array_key_exists('quantityAsFloat', $defaults->describeTag('QTY')?->accessors() ?? []));

echo "docs/llms/extending.md OK\n";
