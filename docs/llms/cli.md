# CLI

`composer require chemaclass/edifact-parser` installs an `edifact` binary.

Runnable parse example: [`example/llms-cli.php`](../../example/llms-cli.php)

```bash
edifact parse order.edi                    # parsed interchange as JSON
edifact inspect order.edi                  # type, counts by tag, line items
edifact validate order.edi                 # rule set chosen from the message type
edifact validate order.edi --rules=ORDERS
edifact segments                           # every registered tag
edifact segments --tag=NAD                 # accessors and return types
edifact diff before.edi after.edi           # segment-level differences
edifact --version                          # {"name": …, "version": "7.1.0"}
edifact help

edifact parse order.edi --pretty           # pretty-printed JSON
cat order.edi | edifact inspect            # stdin when no path is given
```

## Output contract

- **stdout carries data only** (JSON). Diagnostics, usage and errors go to **stderr**, so
  `edifact parse x.edi | jq` is always safe.
- **Exit codes**: `0` success or valid, `1` invalid input, a failed validation, or a `diff`
  that found differences, `2` usage error (unknown command, unknown rule set, no input, a file that cannot be read).
- `--pretty` changes formatting only, never content.

## Shapes

```jsonc
// parse
{"globalSegments": [
  {"tag": "UNB", "subId": "UNOC", "rawValues": ["UNB", ["UNOC", "3"], "SENDER", "RECIPIENT", ["20191011", "1200"], "REF"]},
  {"tag": "UNZ", "subId": "1", "rawValues": ["UNZ", "1", "REF"]}
], "messages": [{"type": "ORDERS", "segments": [
  {"tag": "UNH", "subId": "1", "rawValues": ["UNH", "1", ["ORDERS", "D", "96A", "UN"]]},
  {"tag": "UNT", "subId": "2", "rawValues": ["UNT", "2", "1"]}
]}], "functionalGroups": [{
  "header": {"tag": "UNG", "subId": "ORDERS", "rawValues": ["UNG", "ORDERS", "S1", "R1", ["20191011", "1200"], "1", "UN", ["D", "96A"]]},
  "messageIndexes": [0],
  "trailer": {"tag": "UNE", "subId": "1", "rawValues": ["UNE", "1", "1"]}
}]}

// inspect
{"messageCount": 2, "messages": [{"message_type": "IFTMIN", "total_segments": 18}]}

// validate
{"valid": false, "messages": [{"message": 0, "type": "ORDERS", "valid": false,
  "diagnostics": [{"code": "segment.required", "severity": "error", "message": "…",
                   "segmentIndex": null, "tag": "BGM", "elementPath": null}]}]}

// diff  (exit 1 when they differ, like diff(1))
{"identical": false, "differences": [
  {"kind": "changed", "message": 0, "tag": "QTY", "subId": "21",
   "before": ["QTY", ["21", "100"]], "after": ["QTY", ["21", "250"]]}]}

// segments --tag=QTY
{"tag": "QTY", "class": "EdifactParser\\Segments\\QTYQuantity",
 "accessors": {"quantityAsFloat": "float"}}
```

Each entry in `messages` follows [`schema/message.schema.json`](../../schema/message.schema.json).

`globalSegments` contains file-level segments such as `UNB` and `UNZ`. Each `functionalGroups` entry has its `UNG` header, `UNE` trailer, and zero-based indexes into `messages`. It is empty when the interchange has no groups.
