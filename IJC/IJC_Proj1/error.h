// error.h
// Reseni IJC-DU1, priklad a)18.3.2016
// Autor: Martin Bobcik, xbobci00, FIT
// Preklad gcc 4.8.1, gcc 4.9.3
#ifndef IJC_ERROR_H
#define IJC_ERROR_H

#include <stdio.h>
#include <stdarg.h>
#include <stdlib.h>
void warning_msg(const char *fmt, ...);
void fatal_error(const char *fmt, ...);
#endif //IJC_ERROR_H
