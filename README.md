# SCTP Testsuite for the partial reliability extension on FreeBSD and Linux based on packetdrill

This testsuite focuses on testing the partial partial reliability extension of SCTP.
Most test cases are developed so that they can test the Linux Kernel Implementation and the FreeBSD
Kernel Implementation.

## Requirements
It it based on the packetdrill testtool.
The original version is available from [Google's repository](https://github.com/google/packetdrill).
However, this version does not really run on FreeBSD.
An extended version is available from [NPLab's respository](https://github.com/nplab/packetdrill)
and overcomes this limitation and adds support for SCTP and UDPLite.

## Structure of the Testsuite
| Test Group                                                                             |   Number of Test Scripts | Status        |
| :------------------------------------------------------------------------------------- | :----------------------: | :-----------: |
| [Handshake with Forward-TSN-supported Parameter](handshake-with-forward-tsn/)          |                        8 | Done          |
| [Receiver Side Implementation](receiver-side-implementation/)                          |                       72 | Done          |
| [Sender Side Implementation](sender-side-implementation/)                              |                       75 | Done          |
| [Error Cases](error-cases/)                                                            |                       27 | In Progress   |
| [I-FORWARD-TSN and I-DATA](i-forward-tsn/)                                             |                      128 | Done          |
| [NR-SACK](nr-sack/)                                                                    |                       77 | In Progress   |

## FreeBSD Fixes
* [r303834](https://svnweb.freebsd.org/changeset/base/303834)
* [r304837](https://svnweb.freebsd.org/changeset/base/304837)
* [r309851](https://svnweb.freebsd.org/changeset/base/309851)

## References
* [Formal definition of test cases](https://xdcc.fallout-hq.de/spec/)
