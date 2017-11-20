// bit_array.h
// Reseni IJC-DU1, priklad a)18.3.2016
// Autor: Martin Bobcik, xbobci00, FIT
// Preklad gcc 4.8.1, gcc 4.9.3

#ifndef IJC_BIT_ARRAY_H
#define IJC_BIT_ARRAY_H

#include "error.h"
#define CHAR_BIT 8
typedef unsigned long bit_array_t[];

#define ba_create(jmeno_pole, velikost)  \
    unsigned long jmeno_pole[((velikost / CHAR_BIT) / sizeof(unsigned long) + 2)] \
    = {velikost, 0}

#define DU1_GET_BIT(p,i)\
p[(i) / (sizeof(unsigned long) * CHAR_BIT) + 1] >> \
        ((i) % (sizeof(unsigned long) * CHAR_BIT )) & ~( ~0UL << 1UL )

#define DU1_SET_BIT(p,i,b)if(b){\
            p[(i) / (sizeof(unsigned long) * CHAR_BIT) +1 ] |= ( 1UL << (i) % ((sizeof(unsigned long) * CHAR_BIT )));\
        }\
        else{\
            p[(i) / (sizeof(unsigned long) * CHAR_BIT) + 1] &= ~( 1UL << (i)  % ((sizeof(unsigned long) * CHAR_BIT)));\
        }

#ifdef USE_INLINE
//ready
#define ba_size(jmeno_pole)\
    (jmeno_pole[0])

//ready
#define ba_get_bit(jmeno_pole, index)\
   ( ((index) < ba_size(jmeno_pole) && (index)>=0) ?\
         DU1_GET_BIT(jmeno_pole,index):\
    (fatal_error("Index %ld mimo rozsah 0..%ld(ba_get_bit)\n", (long)index, ba_size(jmeno_pole)),0))

//ready
#define ba_set_bit(jmeno_pole,index,vyraz)do{\
    if((index)<ba_size(jmeno_pole) && (index)>=0 ){\
        DU1_SET_BIT(jmeno_pole,index,vyraz);\
    }\
    else{\
        fatal_error("Index %ld mimo rozsah 0..%ld\n", (long)index+1, ba_size(jmeno_pole));\
    }}while(0)

//not needed
#define bitwise_print(jmeno_pole) {\
    int i;\
    for(i=ba_size(jmeno_pole)-1;i >=0 ;i--){\
        int bit = ba_get_bit(jmeno_pole,i);\
        printf("%d",bit);}\
    printf("\n");}

#else
void static inline ba_set_bit(bit_array_t jmeno_pole,int index,int vyraz);
unsigned long static inline ba_size(bit_array_t jmeno_pole);
int static inline ba_get_bit(bit_array_t jmeno_pole,long index);
void static inline bitwise_print(bit_array_t jmeno_pole);

void static inline ba_set_bit(bit_array_t jmeno_pole,int index,int vyraz){
    if(index<ba_size(jmeno_pole) && (index)>=0){
        DU1_SET_BIT(jmeno_pole,index,vyraz);
    }
    else{
        fatal_error("Index %ld mimo rozsah 0..%ld\n", (long)index+1, ba_size(jmeno_pole));
    }
}

unsigned long static inline ba_size(bit_array_t jmeno_pole){
    return jmeno_pole[0];
}

int static inline ba_get_bit(bit_array_t jmeno_pole,long index){
 return ((index) < ba_size(jmeno_pole) && (index)>=0) ?\
         DU1_GET_BIT(jmeno_pole,index):\
    (fatal_error("Index %ld mimo rozsah 0..%ld\n", (long)index, ba_size(jmeno_pole)),0);
}

void static inline bitwise_print(bit_array_t jmeno_pole) {\
    int i;
    for(i = ba_size(jmeno_pole)-1;i >= 0 ;i--){
        int bit = ba_get_bit(jmeno_pole,i);
        printf("%d",bit);
    }
    printf("\n");
}
#endif
#endif
