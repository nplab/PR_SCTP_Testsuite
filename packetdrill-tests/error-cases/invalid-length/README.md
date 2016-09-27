# Status of the Error Cases - Invalid Length Tests

| Name                                           | Implemented   | Finalized FreeBSD   | Finalized Linux   |
| :--------------------------------------------: | :-----------: | :-----------------: | :---------------: |
| forward-tsn-tlv-too-long                       | Yes           | Yes (Note 1)        | Yes               |
| forward-tsn-tlv-too-short                      | Yes           | Yes (Note 2)        | Yes (Note 2)      |
| forward-tsn-too-long                           | Yes           | Yes (Note 3)        | Yes (Note 3)      |
| forward-tsn-too-short                          | Yes           | Yes (Note 3)        | Yes (Note 3)      |
| init-with-forward-tsn-tlv-too-long             | Yes           | Yes                 | Yes               |
| init-with-forward-tsn-tlv-too-short            | Yes           | Yes (Note 4)        | Yes               |
| init-with-forward-tsn-too-long                 | Yes           | Yes                 | Yes (Note 4)      |
| init-with-forward-tsn-too-short                | Yes           | Yes (Note 4)        | Yes               |

# Notes
1. Instead of sending an ABORT-Chunk it silently discards the invalid Chunk.
2. packetdrill crashes with error message `packetdrill: sctp_iterator.c:81: sctp_chunks_next: Assertion `chunk_length >= sizeof(struct sctp_chunk)' failed.`
3. Instead of sending an ABORT-Chunk the implementation sends a SACK-Chunk.
4. Instead of sending an ABORT-Chunk the implementation sends an INIT-ACK-Chunk.

