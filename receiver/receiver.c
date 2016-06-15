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

int main(int argc, char **argv) {
  int sock;

  sock = socket(AF_INET, SOCK_SEQPACKET, IPPROTO_SCTP);
  if (sock == -1) {
    perror("socket");
    return 1;
  }

  struct sctp_initmsg initmsg;
  initmsg.sinit_num_ostreams = 3;
  initmsg.sinit_max_instreams = 3;
  initmsg.sinit_max_attempts = 3;
  initmsg.sinit_max_init_timeo = 500;

  if (setsockopt(sock, IPPROTO_SCTP, SCTP_INITMSG, &initmsg, sizeof(initmsg))) {
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
  if (setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &val, sizeof(val)) == -1) {
     perror("setsockopt");
  }

  struct sockaddr_in s_addr;
  memset(&s_addr, 0, sizeof(struct sockaddr_in));
  s_addr.sin_family = AF_INET;
  s_addr.sin_addr.s_addr = htonl(INADDR_ANY);
  s_addr.sin_port = htons(5555);

  int ret = bind(sock, (const struct sockaddr *)&s_addr, sizeof(s_addr));

  if (ret == -1) {
    perror("bind");
  }

  ret = listen(sock, 10);

  if (ret == -1) {
    perror("listen");
  }

  int client_fd = -1;

  unsigned char buffer[BUFSIZ];

  struct msghdr msg;
  struct iovec iov[1];
  memset(&iov, 0, sizeof(iov));
  memset(&msg, 0, sizeof(msg));
  ssize_t bytesRcvd = 0;

  iov->iov_base = buffer;
  iov->iov_len = BUFSIZ;
  msg.msg_iov = iov;
  msg.msg_iovlen = 1;

  while (1) {
    do {
      bytesRcvd = recvmsg(sock, &msg, 0);
      if (bytesRcvd == -1) {
        perror("recvmsg");
      } else if (bytesRcvd == 0) {
        printf("connection finished, closing fd now!\n");
        close(sock);
      } else {
        printf("got %zd bytes!\n", bytesRcvd);
      }
    } while (bytesRcvd > 0);
  }

  return 0;
}
