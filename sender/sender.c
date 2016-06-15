#include <arpa/inet.h>
#include <errno.h>
#include <netinet/in.h>
#include <netinet/sctp.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/param.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <unistd.h>

#define BUFLEN BUFSIZ
#define SEND_ITERATIONS 100

int main(int argc, char **argv) {
  int sock;
  char msg[BUFLEN];

  if (argc != 2) {
    printf("./client <ip-addr>\n");
    return EXIT_FAILURE;
  }

  if (inet_addr(argv[1]) == 0) {
    printf("invalid ip address!\n");
    return EXIT_FAILURE;
  }

  sock = socket(AF_INET, SOCK_SEQPACKET, IPPROTO_SCTP);
  if (sock == -1) {
    perror("socket");
  }

  struct sockaddr_in s_addr;
  memset(&s_addr, 0, sizeof(struct sockaddr_in));
  s_addr.sin_family = AF_INET;
  s_addr.sin_addr.s_addr = inet_addr(argv[1]);
  s_addr.sin_port = htons(5555);

  int adata_len = CMSG_SPACE(sizeof(struct sctp_prinfo)) 
                  /*+ CMSG_SPACE(sizeof(struct sctp_sndrcvinfo))*/;

  char *adata = malloc(adata_len);
  if (adata == NULL) {
    perror("malloc");
    return 1;
  }

  memset(adata, 0, adata_len);
  struct msghdr header;
  memset(&header, 0, sizeof(struct msghdr));

  struct iovec iov[1];
  memset(&iov, 0, sizeof(iov));

  /* fill msg structure */
  iov->iov_base = msg;
  iov->iov_len = BUFLEN;
  header.msg_control = adata;
  header.msg_controllen = adata_len;
  header.msg_iov = iov;
  header.msg_iovlen = 1;

  struct cmsghdr *c_header;

  /*  pr_info */
  c_header = CMSG_FIRSTHDR(&header);
  c_header->cmsg_len = CMSG_LEN(sizeof(struct sctp_prinfo));
  c_header->cmsg_level = IPPROTO_SCTP;
  c_header->cmsg_type = SCTP_PRINFO;

  struct sctp_prinfo *pr_info = (struct sctp_prinfo *)CMSG_DATA(c_header);
  pr_info->pr_policy = SCTP_PR_SCTP_TTL;
  pr_info->pr_value = 1;

#if 0
 /* sndrcv */
  c_header = CMSG_NXTHDR(&header, c_header);
  c_header->cmsg_len = CMSG_LEN(sizeof(struct sctp_sndrcvinfo));
  c_header->cmsg_level = IPPROTO_SCTP;
  c_header->cmsg_type = SCTP_SNDRCV;

  struct sctp_sndrcvinfo* sri = (struct sctp_sndrcvinfo*) CMSG_DATA(c_header);
  sri->sinfo_stream = 1;
  sri->sinfo_flags = SCTP_UNORDERED;
#endif

  struct sctp_initmsg initmsg;
  initmsg.sinit_num_ostreams = 3;
  initmsg.sinit_max_instreams = 3;
  initmsg.sinit_max_attempts = 3;
  initmsg.sinit_max_init_timeo = 2000;

  if (setsockopt(sock, IPPROTO_SCTP, SCTP_INITMSG, &initmsg, sizeof(initmsg)) == -1) {
    perror("setsockopt");
  }

  struct sctp_default_prinfo default_pr;
  default_pr.pr_policy = SCTP_PR_SCTP_TTL;
  default_pr.pr_value = 1;
  default_pr.pr_assoc_id = SCTP_ALL_ASSOC;

  if (setsockopt(sock, IPPROTO_SCTP, SCTP_DEFAULT_PRINFO, &default_pr,
                 sizeof(default_pr)) == -1) {
    perror("setsockopt");
  }

  int val = 1;
  if (setsockopt(sock, IPPROTO_SCTP, SCTP_NODELAY, &val, sizeof(val)) == -1) {
    perror("setsockopt");
  }

  header.msg_name = &s_addr;
  header.msg_namelen = sizeof(s_addr);

  printf("sending %d times %d bytes of user data now...\n", SEND_ITERATIONS, BUFLEN);

  for (int i = 0; i < SEND_ITERATIONS; i++) {
    if (sendmsg(sock, &header, 0) == -1) {
      perror("sendmsg");
    }
  }

  printf(("done.\n"));

  return 0;
}
