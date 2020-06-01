# UNB, Interchange Header

### Function

To start, identify and specify an interchange

```
 Ref.   Repr.       Name                    Remarks

 S001           M   SYNTAX IDENTIFIER
 0001   a4      M   Syntax identifier      a3, upper case
                                           Controlling Agency (e.g.
                                           UNO=UN/ECE) and a1 stating
                                           level (e.g. A)  (which
                                           together give UNOA)
 0002   n1      M   Syntax version number  Increments 1 for each new
                                           version. Shall be 2 to
                                           indicate this version
 ___________________________________________________________________

 S002           M   INTERCHANGE SENDER
 0004   an..35  M   Sender identification  Code or name as specified
                                           in IA
 0007   an..4   C   Partner identification Used with sender
                    code qualifier         identification code
 0008   an..14  C   Address for reverse
                    routing
 ___________________________________________________________________

 S003           M   INTERCHANGE RECIPIENT
 0010   an..35  M   Recipient Identification  Code or name as
                                              specified in IA
 0007   an..4   C   Partner identification    Used with recipient
                    code qualifier            identification code
 0014   an..14  C   Routing address           If used, normally coded
                                              sub-address for onward
                                              routing
 ___________________________________________________________________

 S004           M   DATE/TIME OF PREPARATION
 0017   n6      M   Date                      YYMMDD
 0019   n4      M   Time                      HHMM
 ___________________________________________________________________

 0020   an..14  M  INTERCHANGE CONTROL        Unique reference
                   REFERENCE                  assigned by sender
 ___________________________________________________________________

 S005           C   RECIPIENTS REFERENCE,
                    PASSWORD
 0022   an..14  M   Recipient's reference/  As specified in IA. May
                    password                be password to
                                            recipient's system or to
                                            third party network
 0025   an2     C   Recipient's reference/  If specified in IA
                    password qualifier
 ___________________________________________________________________

 0026   an..14  C   APPLICATION REFERENCE     Optionally message
                                              identification if the
                                              interchange contains
                                              only one type of
                                              message
 ___________________________________________________________________

  0029   a1      C   PROCESSING PRIORITY CODE  Used if specified in
                                               IA
 ___________________________________________________________________

 0031   n1      C   ACKNOWLEDGEMENT REQUEST   Set = 1 if sender
                                              requests
                                              acknowledgement, i.e.
                                              UNB and UNZ
                                              segments received
                                              and identified
 ___________________________________________________________________

 0032   an..35  C   COMMUNICATIONS AGREEMENT  If used, to identify
                    ID                        type of communication
                                              agreement controlling
                                              the interchange,
                                              e.g. Customs or ECE
                                              agreement. Code or
                                              name as specified in
                                              IA
 ___________________________________________________________________

 0035   n1      C   TEST INDICATOR            Set = 1 if the
                                              interchange is a test.
                                              Otherwise not used
                                              used
```
[(source)](http://www.unece.org/fileadmin/DAM/trade/edifact/untdid/d422_s.htm#structures)
