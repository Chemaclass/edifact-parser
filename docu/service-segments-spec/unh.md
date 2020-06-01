[Go back to the list](README.md)

# UNH, Message Header

### Function

To head, identify and specify a Message

```
 Ref.   Repr.       Name                         Remarks

 0062   an..14  M   MESSAGE REFERENCE NUMBER   A sender's unique
                                               message reference
 ___________________________________________________________________

 S009           M   MESSAGE IDENTIFIER
 0065   an..6   M   Message type              Type of message being
                                              transmitted
 0052   an..3   M   Message version number    Version number of the
                                              message type. If UNG is
                                              used, 0052 shall be
                                              identical
 0054   an..3   M   Message release number    Release number within
                                              current version number
 0051   an..2   M   Controlling agency        Code to identify the
                                              agency controlling the
                                              specification,
                                              maintenance and
                                              publication of the
                                              message type
 0057   an..6   C   Association assigned      A code assigned
                    code                      by the association
                                              responsible for the
                                              design and maintenance
                                              of the message type
 ___________________________________________________________________

 0068   an..35  C   COMMON ACCESS REFERENCE  Key to relate all
                                             subsequent transfers of
                                             data to the same
                                             business case of
                                             file. Within the
                                             35 characters the
                                             IA may specify
                                             component elements
 ___________________________________________________________________

 S010           C   STATUS OF THE TRANSFER
 0070   n..2    M   Sequence of transfers      Starts at 1 and is
                                               incremented by 1 for
                                               each transfer
 0073   a1      C   First and last transfer    C = Creation, must be
                                               present for first
                                               transfer if more than
                                               one foreseen
                                               F = Final, must be
                                               present for last
                                               transfer

 *) Not required if provided in UNG
```
[(source)](http://www.unece.org/fileadmin/DAM/trade/edifact/untdid/d422_s.htm#structures)
