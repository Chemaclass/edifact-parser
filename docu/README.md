# EDIFACT

* [Segments in EDIFACT?](segments.md)
* [Service Segments Specification](service-segments-spec/README.md)
  * [UNA](service-segments-spec/una.md) (separators, delimiters...) 
  * [UNB](service-segments-spec/unb.md) (file header)
  * [UNG](service-segments-spec/ung.md) (group start)
  * [UNH](service-segments-spec/unh.md) (message header)
  * [UNT](service-segments-spec/unt.md) (message end)
  * [UNE](service-segments-spec/une.md) (group end)
  * [UNZ](service-segments-spec/unz.md) (file end)

## What is EDIFACT?

The UN/EDIFACT Syntax Rules were approved in 1987 as the "ISO 9735" by the 
International Organization for Standardization.

`EDIFACT` stands for "Electronic Data Interchange For Administration, Commerce and Transport". 

The `EDIFACT` standard provides:

* a set of syntax rules to structure data
* an interactive exchange protocol (I-EDI)
* standard messages which allow multi-country and multi-industry exchange

The `EDIFACT` standards cover transaction sets (the business documents that you wish to transmit), 
data element directories and syntax rules which cover delimiter characters etc.

* An `EDIFACT` electronic transmission consists of one or more Interchanges. 
* Each Interchange may consist of one or more Messages. 
* These Messages contain segments of data relating to the business transaction. 
* At each level, a series of enveloping data pairs keep track of the exchange structure.

## Service Segments

All of these `EDIFACT` messages have the same basic structure, consisting of a sequence of segments:

```
UNA – separators, delimiters and special characters are defined for the interpreting software 
UNB – file header (with the file end "UNZ" this makes up the envelope, containing basic information)
UNG – group start
UNH – message header
UNT – message end
UNE – group end
UNZ – file end
```

You can also visualize these lines as something like:

```
 |_Service String Advice              UNA  Optional
 |____Interchange Header              UNB  Mandatory
 :    |___Functional Group Header     UNG  Conditional
 :    :   |___Message Header          UNH  Mandatory
 :    :   :   |__ User Data Segments          As required
 :    :   |__ Message Trailer         UNT  Mandatory
 :    |__ Functional Group Trailer    UNE  Conditional
 |___ Interchange Trailer             UNZ  Mandatory
```

## Structure

`EDIFACT` has a hierarchical structure where the **top level is referred to as an interchange**, 
and **lower levels contain multiple messages which consist of [segments](segments.md)**, which in turn consist 
of composites. The final iteration is an element which is derived from the United Nations Trade 
Data Element Directory (UNTDED); these are normalised throughout the `EDIFACT` standard.

### Examples

* An EDIFACT EDI Purchase Order [(source)](https://www.edi-plus.com/resources/message-formats/edifact/)

```
UNB+UNOA:1+US::US+50138::THEM+140531:0305+001934++ORDERS'
UNH+1+ORDERS:91:2:UN'
BGM+220+A761902+4:20140530:102+9'
RFF+CT:EUA01349'
RFF+AAV::C'
TXT+THIS IS WHAT AN EDI MESSAGE WOULD LOOK LIKE... '
NAD+BY++OUR NAME PLC::::+++++EW4 34J'
CTA+PD'
COM+01752 253939:TE+01752 253939:FX+0:TL'
CTA+OC+:A.SURNAME'
COM+2407:EX'
CTA+TI+:B.BROWN'
COM+0:EX'
CTA+SU'
COM+0161 4297476:TE+01752 670633:FX'
UNT+15+1'
UNZ+1+001934'
```

* Used to answer a flight ticket (FRA-JFK-MIA) availability request [(source)](https://en.wikipedia.org/wiki/EDIFACT)

```
UNA:+.? '
UNB+IATB:1+6XPPC:ZZ+LHPPC:ZZ+940101:0950+1'
UNH+1+PAORES:93:1:IA'
MSG+1:45'
IFT+3+XYZCOMPANY AVAILABILITY'
ERC+A7V:1:AMD'
IFT+3+NO MORE FLIGHTS'
ODI'
TVL+240493:1000::1220+FRA+JFK+DL+400+C'
PDI++C:3+Y::3+F::1'
APD+74C:0:::6++++++6X'
TVL+240493:1740::2030+JFK+MIA+DL+081+C'
PDI++C:4'
APD+EM2:0:1630::6+++++++DA'
UNT+13+1'
UNZ+1+1'
```

## References

* [EDIFACT | Wikipedia](https://en.wikipedia.org/wiki/EDIFACT)
* [Message formats | Edi-plus](https://www.edi-plus.com/resources/message-formats/edifact/)
* [Edifile formats explained | ecosio](https://ecosio.com/en/blog/edi-file-formats-explained/)
* [Structure of an Edifact file | unece](https://ecosio.com/en/blog/edi-standards-overview-structure-of-an-edifact-file/)
* [Syntax Rules | unece](http://www.unece.org/fileadmin/DAM/trade/edifact/untdid/d422_s.htm)
