# EDIFACT

## What is EDIFACT?

The information below will provide a brief introduction to the EDIFACT EDI standard.

EDIFACT stands for Electronic Data Interchange For Administration, Commerce and Transport. EDIFACT is accepted as 
the international EDI standard that has been adopted by organisations wishing to trade in a global context. A standard 
set of syntax rules have been ratified by the United Nations. The EDIFACT standards cover transaction sets (the business
documents that you wish to transmit), data element directories and syntax rules which cover delimiter characters etc.

An EDIFACT electronic transmission consists of one or more Interchanges. Each Interchange may consist of one or more 
Messages. These Messages contain segments of data relating to the business transaction. At each level, a series of 
enveloping data pairs keep track of the exchange structure.

Example `EDIFACT EDI Purchase Order`:

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

## Service Segments

Are used to keep track of the transmission. The most common set is shown below.

```
UNB - Start of Interchange
UNG - Start of Group
UNH - Start of Message
UNT - End of Message
UNE - End of Group
UNZ - End of Interchange
```

### References

* [Edi-plus](https://www.edi-plus.com/resources/message-formats/edifact/)
