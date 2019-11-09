# EDIFACT Parser

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Chemaclass/EdifactParser/build-status/master)

EDIFACT stands for Electronic Data Interchange For Administration, Commerce, and Transport. 

This repository contains a parser for any EDIFACT file to extract the values from any segment
defined in an EDIFACT formatted file. [What is EDIFACT?](/docu/what-is-edifact.md)

## Format of an EDIFACT file

* Each line of the file consists of a set of data that belongs to a specific segment of a message.
* A segment is defined by its name (the first 3chars of the line), following up by the "sub-segment-key". 
Following the rest of the data that belongs to that segment. See more about segments [here](/docu/segments.md).
* A message is a list of segments. Usually, all segments between the UNH and UNT segments compound a message.
* A transaction is the list of messages that belongs to a file. 
