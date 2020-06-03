[Go back to the list](service-segments-spec.md)

# TXT, Text

### Function

To give information in addition to that in other segments
in the service message, as required

> NOTE: Can not be machine processed. Should be avoided if not
necessarily required. Normally a conditional segment. It may repeat
up to the number of times indicated in the message specification
which may not be higher than 5.
```
 Ref.   Repr.       Name                       Remarks

 0077   an3     C   TEXT REFERENCE CODE        Qualifies and
                                               identifies the
                                               purpose and function
                                               of the segment
                                               if indicated in the
                                               message specification
 ___________________________________________________________________

 0078   an..70  M   FREE FORM TEXT          Not machine-processable
                                            information
```
[(source)](http://www.unece.org/fileadmin/DAM/trade/edifact/untdid/d422_s.htm#structures)
