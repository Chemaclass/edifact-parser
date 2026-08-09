# Extending

Runnable version: [`example/llms-extending.php`](../../example/llms-extending.php)

## Custom segments

Extend `AbstractSegment`, return the tag, and use the protected helpers to read elements.

```php
use EdifactParser\Segments\AbstractSegment;

final class EQDEquipmentDetails extends AbstractSegment
{
    public function tag(): string
    {
        return 'EQD';
    }

    public function equipmentTypeCode(): string
    {
        return $this->element(1);          // simple element
    }

    public function identification(): string
    {
        return $this->firstComponent(2);   // first component, or the element if simple
    }

    public function sizeType(): string
    {
        return $this->component(0, 3);     // component 0 of element 3
    }
}
```

Helpers available to subclasses: `element(int)`, `component(int $index, int $group = 1)`,
`firstComponent(int $group)`, `requiredSubId()`.

Register it:

```php
use EdifactParser\Segments\SegmentFactory;

$factory = SegmentFactory::withAdditionalSegments(['EQD' => EQDEquipmentDetails::class]);
$parser = new EdifactParser($factory);
```

- `withAdditionalSegments()` keeps the 32 defaults; a custom class under a default tag
  overrides it.
- `withSegments()` takes an explicit, closed set instead.
- `SegmentFactory::ENVELOPE_SEGMENTS` (7) and `BUSINESS_SEGMENTS` (25) compose:
  `withSegments(SegmentFactory::ENVELOPE_SEGMENTS + ['NAD' => NADNameAddress::class])`.

## Grouping rules

Which tags open a context, attach as children, or close a line-item section.

```php
use EdifactParser\GroupingRules;

$rules = GroupingRules::default()
    ->withContextTags(['NAD', 'LIN'])
    ->withChildTags(['CTA', 'COM', 'DTM'])
    ->withBreakLineItemTags(['UNS', 'CNT', 'UNT']);

EdifactParser::createWithDefaultSegments($rules);

$rules->contextTags();
$rules->childTags();
$rules->breakLineItemTags();
$rules->isContextTag('NAD');

GroupingRules::DEFAULT_CONTEXT_TAGS;
GroupingRules::DEFAULT_CHILD_TAGS;
GroupingRules::DEFAULT_BREAK_LINE_ITEM_TAGS;
```

This is a heuristic, not the UNTDID segment-group structure.

## Custom tokenizer

```php
use EdifactParser\Tokenizer\TokenizerInterface;

final class MyTokenizer implements TokenizerInterface
{
    /** @return list<array<mixed>> */
    public function tokenize(string $content): array
    {
        // element 0 is the tag; other entries are strings or lists of components
        return [['UNH', '1', ['ORDERS', 'D', '96A', 'UN']]];
    }
}

new EdifactParser(SegmentFactory::withDefaultSegments(), tokenizer: new MyTokenizer());
```

## Introspection

Discover what the parser knows, instead of reading the source.

```php
$factory = SegmentFactory::withDefaultSegments();

$factory->registeredTags();     // ['BGM', 'CNT', …] — loads no classes
$factory->classForTag('NAD');   // NADNameAddress::class, or null
$factory->describeTag('QTY')?->accessors();
// ['measureUnit' => 'string', 'qualifier' => 'string',
//  'quantity' => 'string', 'quantityAsFloat' => 'float']
```
