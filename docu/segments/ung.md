[Go back to the list](service-segments-spec.md)

# UNG, Functional Group Header

### Function

To head, identify and specify a Functional Group

```
 Ref.   Repr.       Name                      Remarks

 0038   an..6   M   FUNCTIONAL GROUP        Identifies the one
                    IDENTIFICATION          message type in the
                                            functional group
 ___________________________________________________________________

 S006           M   APPLICATION SENDER'S
                    IDENTIFICATION
 0040   an..35  M   Application sender's    Code or name identifying
                    identification          the division, department
                                            etc. within the
                                            originating sender's
                                            organization
 0007   an..4   C   Partner identification  May be used if sender
                    code qualifier          identification is a code
 ___________________________________________________________________

 S007           M   APPLICATION RECIPIENTS
                    IDENTIFICATION
 0044   an..35  M   Recipient's               Code or name
                    identification            identifying
                                              the division,department
                                              etc. within the
                                              recipients organization
                                              for which the group of
                                              messages is intended
 0007   an..4   C   Recipients                May be used if
                    identification qualifer   recipient
                                              identification is
                                              a code
 ___________________________________________________________________

 S004           M   DATE/TIME OF PREPARATION
 0017   n6      M   Date                         YYMMDD
 0019   n4      M   Time                         HHMM
 ___________________________________________________________________

 0048   an..14  M   FUNCTIONAL GROUP REFERENCE  Unique reference
                    NUMBER                      number assigned
                                                by sender's
                                                division, department
                                                etc.
 ___________________________________________________________________

 0051   an..2   M   CONTROLLING AGENCY        Code to identify the
                                              agency controlling the
                                              specification,
                                              maintenance and
                                              publication of the
                                              message type
 ___________________________________________________________________

 S008           M   MESSAGE VERSION
 0052   an..3   M   Message version number    Version number of the
                                              message type the
                                              functional group
 0054   an..3   M   Message release number    Release number within
                                              current version number
 0057   an..6   C   Association assigned Code A code assigned by the
                                              association responsible
                                              for the design and
                                              maintenance of the type
                                              of message concerned
 ___________________________________________________________________

 0058   an..14  C   APPLICATION PASSWORD      Password to recepient's
                                              division, department or
                                              sectional system (if
                                              required)
```
