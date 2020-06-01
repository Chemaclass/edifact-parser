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
an1    M     Reserved for future    Insert space character
             use
an1    M     SEGMENT TERMINATOR
```
[(source)](http://www.unece.org/fileadmin/DAM/trade/edifact/untdid/d422_s.htm#structures)
