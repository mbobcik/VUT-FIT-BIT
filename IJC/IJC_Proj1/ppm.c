// ppm.c
// Reseni IJC-DU1, priklad a)18.3.2016
// Autor: Martin Bobcik, xbobci00, FIT
// Preklad gcc 4.8.1, gcc 4.9.3

#include "ppm.h"


//načte obsah PPM souboru do touto funkcí dynamicky
//alokované struktury. Při chybě formátu použije funkci warning_msg
//a vrátí NULL.  Pozor na "memory leaks".
struct ppm * ppm_read(const char * filename){
    return NULL;
}

//zapíše obsah struktury p do souboru ve formátu PPM.
//Při chybě použije funkci warning_msg a vrátí záporné číslo.
int ppm_write(struct ppm *p, const char * filename){
    return -1;
}
