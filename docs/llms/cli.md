# CLI

`composer require chemaclass/edifact-parser` installs an `edifact` binary.

```bash
edifact parse order.edi                    # parsed interchange as JSON
edifact inspect order.edi                  # type, counts by tag, line items
edifact validate order.edi                 # rule set chosen from the message type
edifact validate order.edi --rules=ORDERS
edifact segments                           # every registered tag
edifact segments --tag=NAD                 # accessors and return types
edifact help

edifact parse order.edi --pretty           # pretty-printed JSON
cat order.edi | edifact inspect            # stdin when no path is given
```

## Output contract

- **stdout carries data only** (JSON). Diagnostics, usage and errors go to **stderr**, so
  `edifact parse x.edi | jq` is always safe.
- **Exit codes**: `0` success or valid, `1` invalid input or a failed validation, `2` usage
  error (unknown command, unknown rule set, no input).
- `--pretty` changes formatting only, never content.

## Shapes

```jsonc
// parse
{"messages": [{"type": "ORDERS", "segments": [{"tag": "UNH", "subId": "1", "rawValues": []}]}]}

// inspect
{"messageCount": 2, "messages": [{"message_type": "IFTMIN", "total_segments": 18}]}

// validate
{"valid": false, "messages": [{"message": 0, "type": "ORDERS", "valid": false,
  "diagnostics": [{"code": "segment.required", "severity": "error", "message": "…",
                   "segmentIndex": null, "tag": "BGM", "elementPath": null}]}]}

// segments --tag=QTY
{"tag": "QTY", "class": "EdifactParser\\Segments\\QTYQuantity",
 "accessors": {"quantityAsFloat": "float"}}
```

The `parse` shape is formally described by [`schema/message.schema.json`](../../schema/message.schema.json).
