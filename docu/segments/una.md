[Go back to the list](service-segments-spec.md)

# UNA, Service String advice

### Function

To define the characters selected for use
as delimiters and indicators in the rest of the
interchange that follows:

* The specifications in the Service string advice take
precedence over the specifications for delimiters etc. in
segment UNB.

* When transmitted, the Service string advice must appear
immediately before the Interchange Header (UNB) segment and
begin with the upper case characters UNA immediately followed
by the six characters selected by the sender to indicate, in
sequence, the following functions:

```
Repr.        Name                   Remarks

an1    M     COMPONENT DATA
             ELEMENT SEPARATOR
an1    M     DATA ELEMENT SEPARATOR
an1    M     DECIMAL NOTATION       Comma or full stop
an1    M     RELEASE INDICATOR      If not used, insert
                                    space character
an1    M     REPETITION SEPARATOR   Syntax version 4 only.
                                    Reserved in version 3 —
                                    insert space character
an1    M     SEGMENT TERMINATOR
```
[(source)](http://www.unece.org/fileadmin/DAM/trade/edifact/untdid/d422_s.htm#structures)

### Syntax version 3 vs 4

Position 5 is the one that differs between syntax versions. Version 3 reserves it and
conventionally carries a space; version 4 uses it as the **repetition separator**, default
`*`, which separates repeats of a single data element.

```
UNA:+.? '     syntax 3 — position 5 reserved
UNA:+.?*'     syntax 4 — '*' separates repetitions
```

The parser honours whichever the interchange declares. Under syntax 4 a repeated element is
split into its repeats; under syntax 3 the same character is ordinary data, so nothing
changes for version 3 traffic:

```
UNA:+.?*'RFF+CU:A*CU:B'   =>   ['RFF', [['CU','A'], ['CU','B']]]
UNA:+.? 'FTX+AAI+a*b'     =>   ['FTX', 'AAI', 'a*b']
```

See [`Serializer\UnaSeparators`](../../src/Serializer/UnaSeparators.php) and
[docs/llms/parsing.md](../../docs/llms/parsing.md#syntax-version-4).
