// primes.c
// Reseni IJC-DU1, priklad a)18.3.2016
// Autor: Martin Bobcik, xbobci00, FIT
// Preklad gcc 4.8.1, gcc 4.9.3

#include <stdio.h>
#include "bit_array.h"
#include "eratosthenes.h"

int main(){
    ba_create(bit1,202000000L);
    Eratosthenes(bit1);
    int savedPrimes = 0;
    unsigned long primes [10] = {0};
    unsigned long i;
    for(i = ba_size(bit1) - 1; i > 1; i--)
    {
        if(ba_get_bit(bit1,i)==(unsigned long)0){
            primes[savedPrimes] = i;
            savedPrimes++;
            if(savedPrimes==10)
                i=1;
        }
    }

    for(i = 0; i< 10; i++)
        printf("%lu\n",primes[9-i]);
}
