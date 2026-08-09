# Validation

Runnable version: [`example/llms-validation.php`](../../example/llms-validation.php)

Validation never throws. An empty result means the message conforms.

```php
use EdifactParser\Validation\MessageRuleSets;
use EdifactParser\Validation\MessageValidator;

$validator = new MessageValidator();

$violations = $validator->validate($message, MessageRuleSets::orders());
$validator->isValid($message, MessageRuleSets::orders());   // bool
$diagnostics = $validator->diagnose($message, MessageRuleSets::orders());
```

## Bundled rule sets

```php
MessageRuleSets::orders();
MessageRuleSets::invoic();
MessageRuleSets::desadv();
MessageRuleSets::iftmin();

MessageRuleSets::names();              // ['ORDERS', 'INVOIC', 'DESADV', 'IFTMIN']
MessageRuleSets::byName('ORDERS');     // ?MessageRuleSet — null when unknown
```

## Custom rule sets

Every real integration has partner-specific rules. Rule sets are immutable; each method
returns a new one.

```php
use EdifactParser\Validation\MessageRuleSet;

$rules = MessageRuleSet::forType('ORDERS')
    ->require('BGM', 'DTM', 'NAD')
    ->occurs('LIN', 1, 999)
    ->occurs('BGM', 1, 1)
    ->inSequence('UNH', 'BGM', 'DTM', 'NAD', 'LIN', 'UNT');
```

## Violations and diagnostics

```php
foreach ($violations as $violation) {
    $violation->segmentTag();   // 'BGM'
    $violation->rule();         // 'required' | 'cardinality' | 'sequence'
    $violation->message();
    $violation->code();         // DiagnosticCode::SEGMENT_REQUIRED
    $violation->toDiagnostic();
    $violation->toArray();
}
```

Parse failures and validation failures share one type:

```php
use EdifactParser\Diagnostics\DiagnosticCode;

foreach ($diagnostics as $d) {
    $d->code();          // stable — match on this, never on message()
    $d->severity();      // 'error' | 'warning'
    $d->segmentIndex();  // ?int
    $d->tag();           // ?string
    $d->elementPath();   // ?string
    $d->toArray();
    (string) $d;
}

DiagnosticCode::SEGMENT_UNTERMINATED;   // 'segment.unterminated'
DiagnosticCode::CHARACTER_NOT_PERMITTED;
DiagnosticCode::RELEASE_INVALID;
DiagnosticCode::TOKENIZE_FAILED;
DiagnosticCode::SEGMENT_REQUIRED;
DiagnosticCode::SEGMENT_CARDINALITY;
DiagnosticCode::SEGMENT_SEQUENCE;
```
