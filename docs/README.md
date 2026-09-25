# Documentation

Start with the [Quick Start](../README.md#quick-start). It installs the package, parses an order, and reads a typed value. If EDIFACT itself is new to you, read the [format primer](../docu/README.md) first.

## Guides

| I want to... | Guide |
| --- | --- |
| Parse a string or file, handle errors, or stream a large file | [Parsing](llms/parsing.md) |
| Read typed values, line items, groups, or differences | [Reading data](llms/reading.md) |
| Build segments and write an interchange | [Writing EDIFACT](llms/writing.md) |
| Validate messages and directory data | [Validation](llms/validation.md) |
| Use the `edifact` command | [Command line](llms/cli.md) |
| Add segments or change grouping | [Extending](llms/extending.md) |

Each guide links to a runnable example in [`example/`](../example). Run all examples with `composer examples`.

## Reference

- [Upgrade from 6.x](../UPGRADING.md)
- [Message JSON schema](../schema/message.schema.json)
- [Agent documentation index](../llms.txt)
- [Contributing](../.github/CONTRIBUTING.md)
