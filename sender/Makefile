CPP=cc
CFLAGS = -std=gnu99 -Wall -g -fstack-protector
#LIBS = -lsctp
LIBS = 
PROG = sender

SRCS = sender.c

all: build

build: $(SRCS)
	$(CPP) $(CFLAGS) -o $(PROG) $(SRCS) $(OBJ_FILES) $(LIBS)

clean:
	rm -f $(PROG)
