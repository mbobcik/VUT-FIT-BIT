// eratosthenes.c
// Reseni IJC-DU1, priklad a)18.3.2016
// Autor: Martin Bobcik, xbobci00, FIT
// Preklad gcc 4.8.1, gcc 4.9.3

#include "eratosthenes.h"
#ifndef IJC_BIT_ARRAY_H
#include "bit_array.h"
#endif

void Eratosthenes(bit_array_t pole){
    int j, i;
    for(i = 0; i < ba_size(pole); i++){
       ba_set_bit(pole, i, 0);
    }
    unsigned long N = ba_size(pole);
    for(i = 2; i <= sqrt(N); i++) {
        if(ba_get_bit(pole, i) == 0){
            for(j = i*2; j < N; j += i)
                ba_set_bit(pole, j, 1);
        }
    }
}
