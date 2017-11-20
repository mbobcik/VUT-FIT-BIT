#Makefile.
#Reseni IJC-DU1, priklad a) 18.3.2016
#Autor: Martin Bobcik, xbobci00,FIT

#parametry prekladu
CC=gcc
CFLAGS=-O2 -std=c99 -lm -Wall -pedantic

all:primes primes-inline steg-decode
#vytvori program primes
primes: primes.o error.o eratosthenes.o
	$(CC) $(CFLAGS) primes.o error.o eratosthenes.o -o primes

primes.o:
	$(CC) $(CFLAGS) -c primes.c 

eratosthenes.o:
	$(CC) $(CFLAGS) -c eratosthenes.c

error.o:
	$(CC) $(CFLAGS) -c error.c

#vytvori program primes-inline
primes-inline: primes-inline.o error-inline.o eratosthenes-inline.o
	$(CC) $(CFLAGS) -DUSE_INLINE primes-inline.o error-inline.o eratosthenes-inline.o -o primes-inline

primes-inline.o:
	$(CC) $(CFLAGS) -DUSE_INLINE -c primes.c -o primes-inline.o

eratosthenes-inline.o:
	$(CC) $(CFLAGS) -DUSE_INLINE -c eratosthenes.c -o eratosthenes-inline.o

error-inline.o:
	$(CC) $(CFLAGS) -DUSE_INLINE -c error.c -o error-inline.o

#todo
steg-decode: 

#smaze objektove soubory
clean:
	rm -f *.o
