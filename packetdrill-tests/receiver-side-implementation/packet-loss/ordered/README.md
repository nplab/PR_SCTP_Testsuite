# Status of the Receicer Side Implementation Packet Loss Ordered Tests

| Name                                           | Implemented   | Finalized FreeBSD   | Finalized Linux   |
| :--------------------------------------------: | :-----------: | :-----------------: | :---------------: |
| ordered-packet-loss-1                          | Yes           | Yes (Note 4)        | Yes (Note 3)      |
| ordered-packet-loss-2                          | Yes           | Yes (Note 2)        | Yes (Note 1)      |
| ordered-packet-loss-3                          | Yes           | Yes (Note 2)        | Yes               |
| ordered-packet-loss-4                          | Yes           | Yes (Note 2)        | Yes               |
| ordered-packet-loss-5                          | Yes           | Yes (Note 2)        | Yes (Note 1)      |
| ordered-packet-loss-6                          | Yes           | Yes (Note 2)        | Yes               |
| ordered-packet-loss-7                          | Yes           | Yes (Note 2)        | Yes               |
| ordered-packet-loss-8                          | Yes           | Yes                 | Yes               |
| ordered-packet-loss-9                          | Yes           | Yes (Note 2)        | Yes               |

# Notes
1. The Linux Kernel Implememtation sends correct but slightly out-of-date informations in the SACK-Chunk, because it seems
   to first process the FORwARD-TSN-Chunk and sends directly a SACK-CHUNK without looking at the bundled DATA-Chunk.
2. The FreeBSD Kernel Implementation does not allow to receive the available user messages before the first fragmented user message was 
   fully received by the kernel.
3. The Linux Kernel does not allow to receive the fully transmitted second user message at stream number 1.
4. The FreeBSD Kernel sends an ABORT instead of an SACK-Chunk.
