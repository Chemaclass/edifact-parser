# Writing EDIFACT

Runnable version: [`example/llms-writing.php`](../../example/llms-writing.php)

## Build segments

```php
use EdifactParser\Segments\NADNameAddress;
use EdifactParser\Segments\Qualifier\NADQualifier;

$nad = NADNameAddress::builder()
    ->withQualifier(NADQualifier::BUYER)
    ->withPartyId('123456')
    ->withName('ACME Corporation')
    ->withCity('Springfield')
    ->withCountryCode('US')
    ->build();
```

`NADNameAddress`, `QTYQuantity` and `PRIPrice` provide `::builder()`. Any segment can also
be constructed directly from raw values: `new QTYQuantity(['QTY', ['21', '100', 'PCE']])`.

## Serialize

```php
use EdifactParser\Serializer\EdifactSerializer;
use EdifactParser\Serializer\UnaSeparators;

$serializer = new EdifactSerializer();

$serializer->serializeSegment($nad);              // "NAD+BY+123456++ACME Corporation…'"
$serializer->serialize([$unh, $bgm, $nad, $unt]); // iterable<SegmentInterface>
$serializer->serialize($message);                 // a TransactionMessage is iterable
$serializer->serialize($segments, includeUna: true);

new EdifactSerializer(new UnaSeparators(component: '#', element: '|'));
```

Separators and the release character are escaped automatically.

## Assemble an interchange

`UNT` and `UNZ` control counts are filled in for you.

```php
use EdifactParser\Writer\InterchangeBuilder;
use EdifactParser\Writer\MessageBuilder;

$message = MessageBuilder::create('1', 'ORDERS')
    ->addSegment($bgm)
    ->addSegment($nad);

$edi = InterchangeBuilder::create('SENDER', 'RECIPIENT', 'REF01')
    ->preparedAt('240101', '1200')
    ->addMessage($message)
    ->toString();
```

`build()` returns `list<SegmentInterface>` if you want the segments rather than a string.

## Qualifier constants

```php
use EdifactParser\Segments\Qualifier\DTMQualifier;
use EdifactParser\Segments\Qualifier\NADQualifier;
use EdifactParser\Segments\Qualifier\PRIQualifier;
use EdifactParser\Segments\Qualifier\QTYQualifier;
use EdifactParser\Segments\Qualifier\RFFQualifier;

NADQualifier::BUYER;        // 'BY'
QTYQualifier::ORDERED;      // '21'
PRIQualifier::CALCULATION_NET;
DTMQualifier::DELIVERY_REQUESTED;
RFFQualifier::ORDER_NUMBER;
```
