<?php

declare(strict_types=1);

/**
 * Executable version of docs/llms/validation.md. Run by CI so the documentation cannot rot.
 */

use EdifactParser\Diagnostics\DiagnosticCode;
use EdifactParser\EdifactParser;
use EdifactParser\Validation\MessageRuleSet;
use EdifactParser\Validation\MessageRuleSets;
use EdifactParser\Validation\MessageValidator;

require __DIR__ . '/../vendor/autoload.php';

$edi = "UNB+UNOC:3+S+R+240101:1200+1'UNH+1+ORDERS:D:96A:UN'UNT+2+1'UNZ+1+1'";
$message = EdifactParser::createWithDefaultSegments()->parse($edi)->transactionMessages()[0];

$validator = new MessageValidator();

// --- Bundled rule sets ------------------------------------------------------
$violations = $validator->validate($message, MessageRuleSets::orders());
assert($violations !== []);                                        // this message lacks BGM
assert($validator->isValid($message, MessageRuleSets::orders()) === false);
assert($validator->diagnose($message, MessageRuleSets::orders()) !== []);

assert(MessageRuleSets::names() === ['ORDERS', 'INVOIC', 'DESADV', 'IFTMIN']);
assert(MessageRuleSets::byName('ORDERS') !== null);
assert(MessageRuleSets::byName('orders') !== null);                // case-insensitive
assert(MessageRuleSets::byName('PAXLST') === null);                // no bundled set
assert(MessageRuleSets::invoic()->messageType() === 'INVOIC');
assert(MessageRuleSets::desadv()->messageType() === 'DESADV');
assert(MessageRuleSets::iftmin()->messageType() === 'IFTMIN');

// --- Custom rule sets -------------------------------------------------------
$rules = MessageRuleSet::forType('ORDERS')
    ->require('BGM', 'DTM', 'NAD')
    ->occurs('LIN', 1, 999)
    ->occurs('BGM', 1, 1)
    ->inSequence('UNH', 'BGM', 'DTM', 'NAD', 'LIN', 'UNT');

assert($rules->messageType() === 'ORDERS');
assert($rules->requiredTags() === ['BGM', 'DTM', 'NAD']);
assert($rules->cardinality()['LIN'] === ['min' => 1, 'max' => 999]);
assert($rules->sequence() !== []);

// A conforming message reports nothing.
$complete = EdifactParser::createWithDefaultSegments()
    ->parse("UNH+1+ORDERS:D:96A:UN'BGM+220'DTM+137:20240101:102'NAD+BY'LIN+1'UNT+6+1'")
    ->transactionMessages()[0];
assert($validator->validate($complete, $rules) === []);
assert($validator->isValid($complete, $rules) === true);

// --- Violations -------------------------------------------------------------
foreach ($violations as $violation) {
    assert($violation->segmentTag() !== '');
    assert(in_array($violation->rule(), ['required', 'cardinality', 'sequence'], true));
    assert($violation->message() !== '');
    assert($violation->code() !== '');
    assert($violation->toDiagnostic()->code() === $violation->code());
    assert(array_key_exists('severity', $violation->toArray()));
}

assert(in_array(DiagnosticCode::SEGMENT_REQUIRED, array_map(static fn ($v) => $v->code(), $violations), true));

// --- Diagnostics ------------------------------------------------------------
foreach ($validator->diagnose($message, MessageRuleSets::orders()) as $diagnostic) {
    assert($diagnostic->code() !== '');
    assert($diagnostic->severity() === 'error');
    assert($diagnostic->isError() === true);
    $diagnostic->segmentIndex();
    assert($diagnostic->tag() !== null);
    $diagnostic->elementPath();
    assert(array_key_exists('code', $diagnostic->toArray()));
    assert((string) $diagnostic !== '');
}

assert(DiagnosticCode::SEGMENT_UNTERMINATED === 'segment.unterminated');
assert(DiagnosticCode::CHARACTER_NOT_PERMITTED !== '');
assert(DiagnosticCode::RELEASE_INVALID !== '');
assert(DiagnosticCode::TOKENIZE_FAILED !== '');
assert(DiagnosticCode::SEGMENT_CARDINALITY !== '');
assert(DiagnosticCode::SEGMENT_SEQUENCE !== '');

echo "docs/llms/validation.md OK\n";
