# Status of the Sender Side Implementation TTL Tests

| Name                                           | Implemented   | Finalized FreeBSD   | Finalized Linux   |
| :--------------------------------------------: | :-----------: | :-----------------: | :---------------: |
| sender-side-implementation-1                   | Yes           | Yes                 | Yes               |
| sender-side-implementation-2                   | Yes           | Yes                 | Yes               |
| sender-side-implementation-3                   | Yes           | Yes                 | Yes               |
| sender-side-implementation-4                   | Yes           | Yes (Note 1)        | Yes               |
| sender-side-implementation-5                   | Yes           | Yes                 | Yes               |
| sender-side-implementation-6                   | Yes           | Yes                 | Yes               |
| sender-side-implementation-7                   | Yes           | Yes                 | Yes (Note 2)      |
| sender-side-implementation-8                   | Yes           | Yes                 | Yes               |
| sender-side-implementation-9                   | Yes           | Yes                 | Yes               |
| sender-side-implementation-10                  | Yes           | Yes                 | Yes               |
| sender-side-implementation-11                  | Yes           | Yes                 | Yes               |
| sender-side-implementation-12                  | Yes           | Yes                 | Yes               |

# Notes
1. The FreeBSD Kernel Implementation does not bundle the FORWARD-TSN-Chunk with the outstanding DATA-Chunk.
2. The Linux Kernel Implementation does not immediately retransmit the outstanding DATA-Chunk after receival of SACK-Chunk.
