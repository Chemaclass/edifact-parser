# Service Segments Specifications

The full description of the data elements in the service
segments is part of ISO 7372 Trade Data Elements Directory
(UNTDED).

* [UNA](una.md) (separators, delimiters...) 
* [UNB](unb.md) (file header)
* [UNG](ung.md) (group start)
* [UNH](unh.md) (message header)
* [UNT](unt.md) (message end)
* [UNE](une.md) (group end)
* [UNZ](unz.md) (file end)
* [UNS](uns.md) (section control)
* [TXT](txt.md) (additional info)

Legend:

```
Ref.   The numeric reference tag for the data element as
       stated in ISO 7372/UNTDED and, when preceded by S,
       reference for a composite data element used in service
       segments

Name   Name of COMPOSITE DATA ELEMENT in capital letters
       Name of DATA ELEMENT in capital letters
       Name of Component data element in small letters

Repr.  Data value representation:
       a       alphabetic characters
       n       numeric characters
       an      alpha-numeric characters
       a3      3 alphabetic characters, fixed length
       n3      3 numeric characters, fixed length
       an3     3 alpha-numeric characters, fixed length
       a..3    up to 3 alphabetic characters
       n..3    up to 3 numeric characters
       an..3   up to 3 alpha-numeric characters

       M       Mandatory element
       C       Conditional element.

       Note that a mandatory component data element in a
       conditional composite data element must appear when the
       composite data element is used


Remarks IA    Interchange Agreement between interchanging
              partners
```

[(source)](http://www.unece.org/fileadmin/DAM/trade/edifact/untdid/d422_s.htm#structures)
