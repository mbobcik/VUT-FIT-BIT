/*
 *   Martin Bobcik
 *   xbobci00
 *   2. project
 */

#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>
#include <math.h>

/*               Prototypes                          */
double taylor_log(double x, unsigned int n);
int myStrlen(const char word[]);
bool myStrcmp(const char string1[],const char string2[]);
double cfrac_log(double x, unsigned int n);
unsigned int eps_cfrag_log(double min, double max, double eps);
double myAbs(double x);
unsigned int eps_taylor_log(double min, double max, double eps);

int argScan(int argc, char * argv[], double *logX, unsigned int * logN,
                     double *iterMin, double *iterMax, double *iterEps);

int mallocAll(double **logX, unsigned int **logN, double **iterMin,
                       double **iterMax, double **iterEps);

void freeAll(double **logX, unsigned int **logN, double **iterMin, double **iterMax,
             double **iterEps);

void processFunction(int programStatus, double *logX, unsigned int * logN,
                     double *iterMin, double *iterMax, double *iterEps);
/*                Prototypes End                      */

/*                Preprocessor                        */
#define IterArgCount 5
#define LogArgCount 4

#define iterArg 0
#define logArg 1
#define errNoArgs 2         //program status
#define errBadArgs 3
#define errMalloc 4
/*                Preprocessor End                    */

unsigned int eps_taylor_log(double min, double max,double eps)
{
    //declaring variables
    //logarythm in actual and last iteration
    double logNow;
    double logBef;
    // number of iterations for minimal and maximal value
    unsigned int iterCountMin = 1;
    unsigned int iterCountMax = 1;
    logNow = taylor_log(min,iterCountMin);
    iterCountMin++;
    do //for min
    {
        logBef = logNow;
        logNow = taylor_log(min,iterCountMin);
        iterCountMin++;
    }while(myAbs(logNow - logBef)>eps);

    logNow = logBef = 0;
    logNow = taylor_log(max,iterCountMax);
    iterCountMax++;
    do //for max
    {
        logBef = logNow;
        logNow = taylor_log(max,iterCountMax);
        iterCountMax++;
    }while(myAbs(logNow - logBef)>eps);

    if(iterCountMax>=iterCountMin)
        return iterCountMax;
    return iterCountMin;
}

unsigned int eps_cfrag_log(double min, double max, double eps)
{
    //declaring variables
    //logarythm in actual and last iteration
    double logNow;
    double logBef;
    // number of iterations for minimal and maximal value
    unsigned int iterCountMin = 1;
    unsigned int iterCountMax = 1;

    logNow = cfrac_log(min,iterCountMin++);
    do //for min
    {
        logBef = logNow;
        logNow = cfrac_log(min,iterCountMin);
        iterCountMin++;
    }while(myAbs(logNow - logBef)>eps);

    logNow = logBef = 0;

    logNow = cfrac_log(max,iterCountMax++);
    do //for max
    {
        logBef = logNow;
        logNow = cfrac_log(max,iterCountMax);
        iterCountMax++;
    }while(myAbs(logNow - logBef)>eps);

    if(iterCountMax>=iterCountMin)
        return iterCountMax;
    return iterCountMin;
}

double cfrac_log(double x, unsigned int n)
{
    //declaring variables
    double z = (x - 1.0) / (x + 1.0);
    double zz = z * z;
    double zlomek = 2 * (double)n - 1;

    unsigned int i;

    //calculating denominator
    for(i = n - 1; i >= 1; i--)
    {
        double iters = i;
        double citatel = iters * iters * zz;
        zlomek = (2 * iters - 1) - (citatel / zlomek);
    }
    return 2 * z / zlomek;
}

/**
* returns absolute value of double float number
* @param x - double number
* @return absolute value
*/
double myAbs(double x)
{
    if (x >= 0)
        return x;
    else
        return - x;
}

double taylor_log(double x, unsigned int n)
{
    double result = 0.0;
    if(x > 0  && x < 1)
    {
        //variables
        x = 1 - x;
        unsigned int i;
        double powX = x;
        //taylors polynom
        for(i = 1; i <= n; i++)
        {
            double iters = i;
            result = result - (powX / iters);
            powX *= x;
        }
    }
    else if(x == 1) //log(1) == 0
    {
        return 0.0;
    }
    else if (x > 1)
    {
        // variables
        x = (x - 1.0) / (double)x;
        unsigned int i;
        double powX = x;
        //taylors polynom
        for(i = 1; i <= n; i++)
        {
            double iters = i;
            result = result + (powX / iters);
            powX *= x;
        }
    }
    return result;
}

/**
*Funkce, ktera zjisti delku pole znaku
*@param word - pole znaku, jehoz delku chceme znat
*@return pozadovana delka pole znaku
*/
int myStrlen(const char word[]) //Projekt1
{
    //deklaruji pomocnou promenou do ktere budu zapisovat delku retezce
    int length = 0;
    //projdu retezec od zacatku po zarazku
    while(word[length] != '\0')
    {
        //inkrementuji pomocnou promennou
        length++;
    }
    //vratim delku retezce
    return length;
}

/**
* Funkce porovna dve pole znaku. Pokud se rovnaji, vrati 1, jinak vrati 0.
*@param string1 - prvni pole znaku
*@param string2 - druhe pole znaku
*@return pravdivostni hodnota rovnosti dvou poli znaku
*/
bool myStrcmp(const char string1[],const char string2[]) //Projekt1
{
    //pokud retezce nemaji stejnou delku, nemohou byt stejne
    if(myStrlen(string1) != myStrlen(string2))
        return false;
    //projdu oba retezce znak po znaku
    int i;
    for(i = 0; i < myStrlen(string1); i++)
    {
        //kdyz se znaky se stejnymy indexy nerovnaji, nejsou retezce stejne
        if(string1[i] != string2[i])
            return false;
    }
    return true;
}


/**
*
*/
int argScan(int argc, char * argv[], double *logX, unsigned int * logN,
                     double *iterMin, double *iterMax, double *iterEps)
{
    if(argc == LogArgCount)
    {
        if(myStrcmp("--log",argv[1]))
        {
            //saving vars for --log argument
            int scanResult = 0;

            scanResult = sscanf(argv[2],"%lf", logX);
            if(scanResult == EOF) //if bad argument found
                return errBadArgs;
            if(*logX <= 0)        // if x == 0
                return errBadArgs;

            scanResult = sscanf(argv[3],"%u", logN);
            if(scanResult == EOF)
                return errBadArgs;
            if(*logN <= 0)       //if number of iterations == 0
                return errBadArgs;


            return logArg;
        }
        else
            return errBadArgs;
    }
    else if(argc == IterArgCount)
    {
        if(myStrcmp("--iter",argv[1]))
        {
            int scanResult = 0;

            //saving vars for --iter argument
            scanResult = sscanf(argv[2],"%lf", iterMin);
            if(scanResult == EOF)
                return errBadArgs;
            if(*iterMin <= 0)
                return errBadArgs;

            scanResult = sscanf(argv[3],"%lf", iterMax);
            if(scanResult == EOF)
                return errBadArgs;
            if(*iterMax <= 0)
                return errBadArgs;

            scanResult = sscanf(argv[4],"%lf", iterEps);
            if(scanResult == EOF)
                return errBadArgs;
            if(*iterEps <= 0)
                return errBadArgs;

            return iterArg;
        }
        else
            return errBadArgs;
    }
    else if(argc >= 2)
       return errBadArgs;
    else
    return errNoArgs;
}

/**
* Allocate memory for all pointers
* @return errNoArgs = all pointers have allocated memory. errMalloc = error during allocation
*/
int mallocAll(double **logX, unsigned int **logN, double **iterMin, double **iterMax, double **iterEps)
{
    *iterMin = malloc(sizeof(double));
    *iterMax = malloc(sizeof(double));
    *iterEps = malloc(sizeof(double));
    *logX = malloc(sizeof(double));
    *logN = malloc(sizeof(unsigned int));

    //if one of pointers is not allocated, return error
    if(iterMin == NULL || iterMax == NULL || iterEps == NULL ||
            logN == NULL || logX == NULL)
    {
        return errMalloc;
    }
    return errNoArgs;
}

/**
* Free all allocated memory
*/
void freeAll(double **logX, unsigned int **logN, double **iterMin, double **iterMax, double **iterEps)
{
    free(*logX);
    free(*logN);
    free(*iterMin);
    free(*iterMax);
    free(*iterEps);
}

/**
* function decide what is the status of the program (preprocessor symbols)
* and print result
*/
void processFunction(int programStatus, double *logX, unsigned int * logN,
                     double *iterMin, double *iterMax, double *iterEps)
{
    if(programStatus == logArg)
    {
        //calculating values for --log argument
        double taylorLogVal = taylor_log(*logX, *logN);
        double cfracLogVal = cfrac_log(*logX, *logN);
        double mathLogVal = log(*logX);

        //printing results
        printf("log(%.4lf) = %.10lf\n", *logX, mathLogVal);
        printf("cf_log(%.4lf) = %.10lf\n", *logX, cfracLogVal);
        printf("taylor_log(%.4lf) = %.10lf\n", *logX, taylorLogVal);
        return;
    }
    else if (programStatus == iterArg)
    {
        // calculating values for --iter arguments
        unsigned int taylorLogIter = eps_taylor_log(*iterMin,*iterMax,*iterEps);
        unsigned int cfracLogIter = eps_cfrag_log(*iterMin, *iterMax, *iterEps);

        double taylorLogValMin = taylor_log(*iterMin, taylorLogIter);
        double cfracLogValMin = cfrac_log(*iterMin, cfracLogIter);
        double mathLogValMin = log(*iterMin);

        double taylorLogValMax = taylor_log(*iterMax, taylorLogIter);
        double cfracLogValMax = cfrac_log(*iterMax, cfracLogIter);
        double mathLogValMax = log(*iterMax);

        //printing results
        printf("log(%.4lf) = %.10lf\n", *iterMin, mathLogValMin);
        printf("log(%.4lf) = %.10lf\n", *iterMax, mathLogValMax);

        printf("continued fraction iterations = %u\n", cfracLogIter);
        printf("cf_log(%.4lf) = %.10lf\n", *iterMin, cfracLogValMin);
        printf("cf_log(%.4lf) = %.10lf\n", *iterMax, cfracLogValMax);

        printf("taylor polynomial iterations = %u\n", taylorLogIter);
        printf("taylor_log(%.4lf) = %.10lf\n", *iterMin, taylorLogValMin);
        printf("taylor_log(%.4lf) = %.10lf\n", *iterMax, taylorLogValMax);
        return;
    }
    else if (programStatus == errBadArgs)//error printing \/
    {
        printf("Error: Bad arguments found\n");
        return;
    }
    else if(programStatus == errNoArgs)
    {
        printf("Error: No arguments found\n");
        return;
    }
}

int main(int argc, char * argv[])
{
    //declaring variables
    int programStatus = errNoArgs;             //int for storing program status
    double *logX, *iterMin, *iterMax, *iterEps;     //iter variables for --iter argument
    unsigned int *logN;                             //log variables for --log argument
    //allocating memory for all pointers
    programStatus = mallocAll(&logX, &logN, &iterMin, &iterMax, &iterEps);

    if(programStatus != errMalloc)
    {
        //scaning command line arguments
        programStatus = argScan(argc, argv, logX,logN, iterMin, iterMax, iterEps);
        //calculating and printing results
        processFunction(programStatus, logX, logN, iterMin, iterMax, iterEps);
    }
    else
        printf("Error: Memory allocation failure\n");

    //freeing memory
    freeAll(&logX, &logN, &iterMin, &iterMax, &iterEps);
    return 0;
}

