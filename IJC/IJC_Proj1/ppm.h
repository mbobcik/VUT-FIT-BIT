// ppm.h
// Reseni IJC-DU1, priklad a)18.3.2016
// Autor: Martin Bobcik, xbobci00, FIT
// Preklad gcc 4.8.1, gcc 4.9.3

#ifndef IJC_PPM_H
#define IJC_PPM_H

#ifndef IJC_ERROR_H
#include "error.h"
#endif

struct ppm {
    unsigned xsize;
    unsigned ysize;
    char data[];
};

struct ppm * ppm_read(const char * filename);
int ppm_write(struct ppm *p, const char * filename);

#endif //IJC_PPM_H
