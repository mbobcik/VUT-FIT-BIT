#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>
#include "error.h"

//exitcodes
#define SUCCESS 0
#define WPARAM 1
#define FOPEN 2

struct params_s{
    FILE *file;
    uintmax_t lines;
};

int getParams(int argc, char *argv[], struct params_s *par){
    if(argc == 1)
    {
        // program nema argument, vytiskne poslednich 10 radku ze stdin
        par->lines=10;
        par->file=stdin;
    }
    else if(argc == 2) {
        //kdyz ma program 1 argument, mela by to byt cesta k souboru
        par->lines=10;
        char *path = argv[1];
        par->file=fopen(path,"r");
        if(par->file == NULL)
            fatal_error(FOPEN,"File opening failed. \n");
    }
    else if(argc == 3) {
        //2 argumenty -n n + stdin
        if (argv[1][0]=='-' && argv[1][1]=='n') {
            par->lines = atoi(argv[2]);
            par->file=stdin;
        }
        else {
            //chyba v argumentu
            fatal_error(WPARAM, "Wrong parameters. \n");
        }
    }
    else if(argc == 4){
        //pocet radku + cesta, nebo naopak
        if (argv[1][0]=='-' && argv[1][1]=='n') {
            par->lines = atoi(argv[2]);
            char *path = argv[3];
            par->file=fopen(path,"r");
            if(par->file == NULL)
                fatal_error(FOPEN,"File opening failed. \n");
        }
        else if(argv[2][0]=='-' && argv[2][1]=='n'){
            par->lines = atoi(argv[3]);
            char *path = argv[1];
            par->file = fopen(path,"r");
            if(par->file == NULL)
                fatal_error(FOPEN,"File opening failed. \n");
        }
        else{
            fatal_error(WPARAM, "Wrong parameters. \n");
        }
    }
    return 0;
}

int main(int argc, char *argv[]) {

    struct params_s *param = malloc(sizeof(struct params_s));
    getParams(argc, argv,param);
    printf("%d",param->lines);
    char soubor[35];
    fscanf(param->file,"%s",soubor);
    printf("%s",soubor);
    return 0;
}