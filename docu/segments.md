# Segments

## EDIFACT Segment Definition

EDIFACT Segment is a collection of logically-related data elements in a fixed, defined sequence. Today’s post gives 
deeper understanding of a segment. As we have spoken before, EDIFACT provides a hierarchical structure for messages. 
EDIFACT messages begin with the Message Header (UNH) Segment and end with the Message Trailer (UNT) Segment. These two 
segments are the first, and innermost, level of the three levels of “electronic envelopes” within EDIFACT.

EDIFACT Segment contains:

* A three-character alphanumeric code that identifies the segment. This is called the segment tag.
* Variable length data elements. These can be either simple or composite.

Segments must be separated by a data element separator (data element delimiter), which is normally + and :, and 
terminated by a segment terminator, normally ‘.

Each EDIFACT Segment is fully documented in the United Nations Trade Data Interchange Directory (UNTDID). These tables 
list the segment position, segment tag and segment name. EDIFACT Segment tables also specify if a segment must appear 
in a message using the requirements designator M (Mandatory) or C (Conditional), and how many times a particular segment
 may repeat (repetition field).

#### In EDIFACT, there are two kinds of segments

* Service Segments
* Generic Segments

#### Service Segments are:

* Envelopes (UNB-UNZ, UNG-UNE, UNH-UNT)
* Delimiter String Advice (UNA)
* Section Separator (UNS)

#### Generic Segments are:

* DOC to identify and specify documents
* MOA for monetary amounts
* DTM for dates and times
* NAD for name and address data.

#### EDIFACT Segment Terminators and Delimiters

The end of each segment is determined by the Data Segment Terminator. In EDIFACT the standard data segment terminator is
 `'`. Optional or conditional data elements that are not used must be accounted for by their position within the segment. 
 However, optional or conditional data elements without data that appear at the end of a data segment do not need 
 additional data element separators to correctly position the data.

### References
* [Ediacademy](https://ediacademy.com/blog/edifact-segment/)
