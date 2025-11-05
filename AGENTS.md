# AGENTS.md

AI context for understanding this EDIFACT parser library architecture.

## Core Architecture

**Parsing flow:** EdifactParser → SegmentFactory → ParserResult

1. **EdifactParser** (`src/EdifactParser.php`)
   - Entry point, uses `sabas/edifact` for low-level parsing
   - Delegates to SegmentFactory for typed segment objects
   - Returns ParserResult

2. **SegmentFactory** (`src/Segments/SegmentFactory.php`)
   - Maps 3-char tags (UNH, NAD, LIN) to segment classes
   - All segments implement SegmentInterface
   - Returns UnknownSegment for unregistered tags

3. **ParserResult** (`src/ParserResult.php`)
   - Contains `globalSegments` (UNA, UNB, UNZ - file-level)
   - Contains `transactionMessages[]` (UNH...UNT message blocks)

4. **TransactionMessage** (`src/TransactionMessage.php`)
   - Single UNH...UNT message block
   - Contains `groupedSegments`, `lineItems[]`, `contextSegments[]`

## Segment Organization

TransactionMessage organizes segments three ways:

1. **groupedSegments**: Flat lookup `['NAD']['CN']` - fastest access
2. **lineItems**: LIN segments with children (products/orders) - created by DetailsSectionBuilder
3. **contextSegments**: Hierarchical parent-child - built by ContextStackParser

## Key Patterns

**Context hierarchy** (`ContextStackParser`):
- Parents: NAD, LIN, DOC
- Children: COM, CTA, PIA, IMD, MEA, QTY, PRI, TAX, DTM, MOA

**Line item boundaries** (`MessageDataBuilder\Builder`):
- Start: LIN segment
- End: UNS, CNT, or UNT segments
- DetailsSectionBuilder groups segments into line items
- SimpleBuilder handles flat grouping

**Segment abstraction**:
- SegmentInterface: `tag()`, `subId()`, `rawValues()`, `parsedSubId()`
- AbstractSegment: Base implementation (handles subId parsing)
- ContextSegment: Decorator with `children()` for hierarchy
- HasRetrievableSegments: Trait for `segmentsByTag()`, `segmentByTagAndSubId()`

**SubId logic**:
- From `rawValues()[1]`
- String `'CN'` or array `['21', 'C62']` → joined as `'21:C62'`
- Used for distinguishing multiple segments with same tag

## Extension Points

- Add custom segments: Extend AbstractSegment, register in SegmentFactory
- Modify context rules: Update ContextStackParser::CONTEXT_TAGS/CHILD_TAGS
- Custom builders: Implement BuilderInterface for different grouping logic

## Commands

```bash
composer test-unit              # Unit tests
composer test-functional        # Functional tests
composer quality                # All checks (CS, Psalm, PHPStan, Rector)
composer csfix                  # Fix code style
```
