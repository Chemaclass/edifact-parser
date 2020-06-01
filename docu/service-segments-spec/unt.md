[Go back to the list](README.md)

# UNT, Message Trailer

### Function

To end and check the completeness of a Message

```
 Ref.   Repr.       Name                       Remarks

 0074   n..6    M   NUMBER OF SEGMENTS IN THE  Control count
                    MESSAGE                    including UNH and UNT
 ___________________________________________________________________

 0062   an..14  M   MESSAGE REFERENCE NUMBER   Shall be identical to
                                               0062 in UNH
```
[(source)](http://www.unece.org/fileadmin/DAM/trade/edifact/untdid/d422_s.htm#structures)
