# Status of the Handshake with Forward-TSN Tests

| Name                                           | Implemented   | Finalized FreeBSD   | Finalized Linux   |
| :--------------------------------------------: | :-----------: | :-----------------: | :---------------: |
| handshake-with-forward-tsn-1                   | Yes           | Yes (Note 4)        | Yes               |
| handshake-with-forward-tsn-2                   | Yes           | Yes                 | Yes               |
| handshake-with-forward-tsn-3                   | Yes           | Yes (Note 4)        | Yes               |
| handshake-with-forward-tsn-4                   | Yes           | Yes                 | Yes (Note 1)      |
| handshake-with-forward-tsn-5                   | Yes           | Yes                 | Yes (Note 2)      |
| handshake-with-forward-tsn-6                   | Yes           | Yes                 | Yes               |
| handshake-with-forward-tsn-7                   | Yes           | No (Note 3)         | No  (Note 3)      |
| handshake-with-forward-tsn-operational-error   | Yes           | Yes                 | Yes               |

# Notes
1. The Linux Kernel Implementation does not send an ERROR-Chunk, instead it silently discards the FORWARD-TSN-Chunk
2. The Linux Kernel Implementation does not send an ERROR-Chunk, it accepts the FORWARD-TSN-Chunk and sends a SACK-Chunk.
3. Due to a limitation in packetdrill not applicable at the moment.
4. The FreeBSD Implementation does not list the FORWARD-TSN-SUPPORTED parameter at the unrecognized parameters.

