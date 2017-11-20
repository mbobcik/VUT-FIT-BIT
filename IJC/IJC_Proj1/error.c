// error.c
// Reseni IJC-DU1, priklad a)18.3.2016
// Autor: Martin Bobcik, xbobci00, FIT
// Preklad gcc 4.8.1, gcc 4.9.3

#include "error.h"
void warning_msg(const char *fmt, ...){
    va_list args;
    va_start(args,fmt);
    printf("CHYBA: ");
    vfprintf(stderr,fmt,args);
    va_end(args);
}

void fatal_error(const char *fmt, ...){
    va_list args;
    va_start(args,fmt);
    fprintf(stderr, "CHYBA: ");
    vfprintf(stderr, fmt,args);
    va_end(args);
    exit(1);
}