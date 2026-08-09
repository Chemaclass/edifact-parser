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

## Directory validation

`MessageValidator` checks message-level rules. `DirectoryValidator` looks *inside* each
segment, against what a UN/EDIFACT directory actually defines: mandatory elements and
composites, representation (`an`/`n`/`a`), maximum lengths, and optionally code lists.

Directory data ships in `php-edifact/edifact-mapping` (a `suggest`, not a requirement):

```bash
composer require --dev php-edifact/edifact-mapping
```

```php
use EdifactParser\Directory\XmlDirectory;
use EdifactParser\Validation\DirectoryValidator;

$directory = XmlDirectory::locate('D96A');            // ?XmlDirectory — null when absent
$directory = XmlDirectory::fromPath('D96A', '/path/to/D96A');

$validator = new DirectoryValidator($directory);
$validator->validate($message);                        // list<Diagnostic>
$validator->isValid($message);
$validator->validateSegment($segment);                 // one segment on its own

// Code lists are opt-in: real traffic uses partner-specific codes.
$validator->withCodeValidation()->validate($message);
```

Diagnostics carry the element path:

```
error [element.required]  at segment 2 (QTY/C186/6060): Data element 6060 is mandatory in QTY
error [element.type]      at segment 3 (QTY/C186/6060): Data element 6060 is 'n' but the value is not
error [element.too-long]  at segment 4 (QTY/C186/6411): Data element 6411 allows at most 3 characters, got 11
error [code.unknown]      at segment 3 (QTY/C186/6063): 'ZZ' is not a listed code for data element 6063
```

Tags the directory does not define are never flagged — unknown and partner-specific
segments stay valid, matching the parser's own permissiveness.

Inspecting the directory directly:

```php
$directory->tags();                       // list<string> — 127 for D96A
$segment = $directory->segment('QTY');
$segment?->name();                        // 'quantity'
$composite = $segment?->partAt(0);         // Composite|DataElement|null
$composite?->elementAt(1)?->maxLength();   // 15
$directory->codesFor('6063');             // ['21' => 'Ordered quantity', …]
```
