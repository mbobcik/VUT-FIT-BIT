//
// Created by Martin Bobcik, xbobci00
// reseni projektu 2 do IOS
// Date: Duben 2016
//

//includy
#include <stdio.h>
#include <ctype.h>
#include <stdlib.h>
#include <wait.h>
#include <time.h>
#include <unistd.h>
#include <signal.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <sys/shm.h>
#include <sys/ipc.h>
#include <sys/stat.h>
#include <sys/sem.h>
#include <semaphore.h>
#include <sys/mman.h>
//KONEC includy

//ErrorCodes
#define E_SUCCESS 0
#define E_PARAMS 1
#define E_FORK 2
#define E_FOPEN 3
#define E_SHM 4
#define E_SEMAPHORE 5
//KONEC ErrorCodes

typedef struct param_s {
    int p;  //pocet pasazeru
    int c;  //kapacita voziku
    int pt; //max doba generace pasazera
    int rt; //max doba prujezdu trati
}param_s;

///////////deklarace funkci
int getParams(int argc, char *argv[], struct param_s *par);
void car();
void passenger();
void passengerCreator();
void myError(int errorCode);
void load();
void unload();
void run();
void board(int internProcessNumber);
void unboard(int internProcessNumber);
void cleanSHM();
int cleanSems();
void cleanUp();
///////////KONEC deklaraci funkci

//////////Globalni promenne
//ukazatel na vystupni soubor
FILE *out;
//struktura s parametrz
param_s parametry;
//pidy
pid_t pidCar, pidPCreator;
//ukazatele na sdilenou pamet
int *shm_CisloAkce, *shm_CisloPassenger, *shm_BoardOrder;
//id sdilene pameti
int shmid_CisloAkce, shmid_CisloPassenger, shmid_BoardOrder; // pro reseni algoritmu s vice vozicky pridat
//semafory                                                   // shm_CisloCar
sem_t *sem_load, *sem_allAboard, *sem_allAshore, *sem_unload, *mutex_CisloPassengerAccess, * mutex_BoardOrderAccess,\
        *mutex_File, *sem_permissionToDie;
/////////KONEC Globalni promenne

int getParams(int argc, char *argv[], struct param_s *par){
    int eCode = E_SUCCESS;
    if(argc != 5)
        return E_PARAMS;
    else{
        if(isdigit(*argv[1])){
            par->p = strtol(argv[1],NULL,10);
            if(par->p <= 0)
                eCode = E_PARAMS;
        }
        if(isdigit(*argv[2])){
            par->c = strtol(argv[2],NULL,10);
            if(par->c <= 0 || par->p <= par->c || par->p % par->c != 0 )
                eCode = E_PARAMS;
        }
        if(isdigit(*argv[3])){
            par->pt = strtol(argv[3],NULL,10);
            if(par->pt < 0 || par->pt >= 5001)
                eCode = E_PARAMS;
        }
        if(isdigit(*argv[4])){
            par->rt = strtol(argv[4],NULL,10);
            if(par->rt < 0 || par->rt >= 5001)
                eCode = E_PARAMS;
        }
    }
    return eCode;
}

void car(){
    sem_wait(mutex_File);
    int cisloAkce =*shm_CisloAkce += 1;
    fprintf(out,"%i\t\t: C 1\t: started\n",cisloAkce);
    sem_post(mutex_File);

    int i;
    for(i=0; i < (parametry.p / parametry.c);i++){  // pro p/c iterac9
        load();     //zavola load a ceka dokud vsichni nenastoupi
        sem_wait(sem_allAboard);
        run();  //vyda se na drahu
        unload(); //zavola unload a ceka dokud vsichni nevystoupi
        sem_wait(sem_allAshore);
    }
    sem_post(sem_permissionToDie); //poslat pasazerum povoleni zemrit

    sem_wait(mutex_File);
    cisloAkce =*shm_CisloAkce += 1;
    fprintf(out,"%i\t\t: C 1\t: finished\n",cisloAkce);
    sem_post(mutex_File);

    exit(EXIT_SUCCESS);
}

void run(){
    sem_wait(mutex_File); //uzamknu zapis
    int cisloAkce =*shm_CisloAkce += 1; //ulozim si cislo aktualni akce
    fprintf(out,"%i\t\t: C 1\t: run\n",cisloAkce); // zapisu do souboru
    sem_post(mutex_File); // odemknu soubor

    if(parametry.rt !=0) {
        int runTime = rand() % parametry.rt * 1000; //spocitam random hodnotu cekani
        // pokud neni maximalni hodnota cekani 0, tak cekam
        usleep((useconds_t) runTime);
    }
}

void unload(){
    sem_wait(mutex_File);
    int cisloAkce =*shm_CisloAkce += 1;
    fprintf(out,"%i\t\t: C 1\t: unload\n",cisloAkce);
    sem_post(mutex_File);
    sem_post(sem_unload);//da pasazerum vedet, ze je mozno vystupovat

}

void load(){
    sem_wait(mutex_File);
    int cisloAkce =*(shm_CisloAkce) += 1;
    fprintf(out,"%i\t\t: C 1\t: load\n",cisloAkce);
    sem_post(mutex_File);
    sem_post(sem_load); // pasazeri moho nastoupit
}

void passengerCreator(){
    pid_t pidPassengers[parametry.p];
    pid_t pidGot;

    int i;
    for(i = 0; i < parametry.p; i++) {
        if(i!=0){
            // cekani na cas vytvoreni passengera
            if(parametry.pt !=0) {
            int generationTime = rand() % parametry.pt * 1000;
                usleep((useconds_t)generationTime);}
        }
        pidGot = fork();
        if (pidGot < 0) {//error fork
            int j;
            for (j = 0; j < i; j++) {       // zabije jiz vytvorene pasazery
                kill(pidPassengers[j], SIGKILL);
            }
            cleanSHM();
            cleanSems();        //vycisti pamet a skonci
            myError(E_FORK);
        }
        else if (pidGot == 0) {// potomek
            passenger();
        }
        else {//puvodni proces
            pidPassengers[i] = pidGot;
        }
    }

    for(i = 0; i < parametry.p; i++)
        waitpid(pidPassengers[i],NULL, 0);  //pocka az skonci vsichni pasazeri a skonci

    exit(EXIT_SUCCESS);
}

void passenger(){
    sem_wait(mutex_CisloPassengerAccess);
    int internProcessNumber = *(shm_CisloPassenger) += 1;
    sem_post(mutex_CisloPassengerAccess); // ulozi si sve interni cislo
                                          //(poradi sveho vytvoreni)
    sem_wait(mutex_File);
    int cisloAkce =*(shm_CisloAkce) += 1;
    fprintf(out,"%i\t\t: P %i\t: started\n",cisloAkce,internProcessNumber);
    sem_post(mutex_File);

    sem_wait(sem_load); // pocka az muze nastoupit

    board(internProcessNumber); //nastoupi

    //uziva si cestu

    sem_wait(sem_unload); // pocka na vystup

    unboard(internProcessNumber); // vystoupi

    sem_wait(sem_permissionToDie); // pocka na cas sve smrti a zemre
    sem_post(sem_permissionToDie);
    sem_wait(mutex_File);
    cisloAkce =*(shm_CisloAkce) += 1;
    fprintf(out,"%i\t\t: P %i\t: finished\n",cisloAkce,internProcessNumber);
    sem_post(mutex_File);

    exit(EXIT_SUCCESS);
}

void board(int internProcessNumber){
    int boardOrder;
    sem_wait(mutex_BoardOrderAccess);   //zjistim poradi vstupu
    boardOrder = *(shm_BoardOrder)+=1;
    sem_post(mutex_BoardOrderAccess);

    sem_wait(mutex_File);
    int cisloAkce =*(shm_CisloAkce) += 1;
    fprintf(out,"%i\t\t: P %i\t: board\n",cisloAkce,internProcessNumber);
    sem_post(mutex_File);

    if(boardOrder!=parametry.c){ // pokud neni posledni, tak nastoupi, a rekne dalsim, ze mohou nastupovat
        sem_wait(mutex_File);
        int cisloAkce =*(shm_CisloAkce) += 1;
        fprintf(out,"%i\t\t: P %i\t: board order %i\n",cisloAkce,internProcessNumber,boardOrder);
        sem_post(mutex_File);
        sem_post(sem_load);
    } else{                     //jinak vystoupi, vynuluje poradi nastupu a ohlasi, ze vsichni jiz nastoupili
        sem_wait(mutex_File);
        int cisloAkce =*(shm_CisloAkce) += 1;
        fprintf(out,"%i\t\t: P %i\t: board order last\n",cisloAkce,internProcessNumber);
        sem_post(mutex_File);

        sem_wait(mutex_BoardOrderAccess);
        *(shm_BoardOrder) = 0;
        sem_post(mutex_BoardOrderAccess);

        sem_post(sem_allAboard);
    }
}

void unboard(int internProcessNumber) {
    int boardOrder;
    sem_wait(mutex_BoardOrderAccess);
    boardOrder = *(shm_BoardOrder)+=1;  //zjisti poradi vystupu
    sem_post(mutex_BoardOrderAccess);

    sem_wait(mutex_File);
    int cisloAkce =*(shm_CisloAkce) += 1;
    fprintf(out,"%i\t\t: P %i\t: unboard\n",cisloAkce,internProcessNumber);
    sem_post(mutex_File);

    if(boardOrder!=parametry.c){
        sem_wait(mutex_File);//pokud neni posledni, tak vystoupi, a rekne dalsim, ze mohou vystupovat
        int cisloAkce =*(shm_CisloAkce) += 1;
        fprintf(out,"%i\t\t: P %i\t: unboard order %i\n",cisloAkce,internProcessNumber,boardOrder);
        sem_post(mutex_File);
        sem_post(sem_unload);

    }else{                  //jinak vystoupi, vynuluje poradi vystupu a ohlasi, ze je vozik prazdny
        sem_wait(mutex_File);
        int cisloAkce =*(shm_CisloAkce) += 1;
        fprintf(out,"%i\t\t: P %i\t: unboard order last\n",cisloAkce,internProcessNumber);
        sem_post(mutex_File);

        sem_wait(mutex_BoardOrderAccess);
        *(shm_BoardOrder) = 0;
        sem_post(mutex_BoardOrderAccess);

        sem_post(sem_allAshore);
    }
}

void myError(int errorCode){
    int exitCode=2;
    switch (errorCode){
        case E_PARAMS:
            exitCode = 1;
            fprintf(stderr,"CHYBA: Byly zadany spatne parametry.\n");
            break;
        case E_FORK:
            fprintf(stderr,"CHYBA: Selhalo systemove volani fork().\n");
            break;
        case E_FOPEN:
            fprintf(stderr,"CHYBA: Selhalo otevirani vystupniho souboru.\n");
            break;
        case E_SHM:
            fprintf(stderr,"CHYBA: Selhala prace se sdilenou pameti.\n");
            break;
        case E_SEMAPHORE:
            fprintf(stderr,"CHYBA: Selhala prace se semafory.\n");
            break;
        default:
            fprintf(stderr,"CHYBA: v programu doslo k chybe.\n");
            break;
    }
    fclose(out);
    exit(exitCode);
}

void cleanSHM(){
    shmctl(shmid_BoardOrder, IPC_RMID, NULL);
    shmctl(shmid_CisloAkce, IPC_RMID, NULL);
    shmctl(shmid_CisloPassenger, IPC_RMID, NULL);
}

int cleanSems(){
    int eCode = E_SUCCESS;
    if (sem_destroy(&(*sem_allAshore))==-1){
        eCode = E_SEMAPHORE;
    }
    if (sem_destroy(&(*sem_allAboard))==-1){
        eCode = E_SEMAPHORE;
    }
    if (sem_destroy(&(*sem_load))==-1){
        eCode = E_SEMAPHORE;
    }
    if (sem_destroy(&(*sem_unload))==-1){
        eCode = E_SEMAPHORE;
    }
    if (sem_destroy(&(*sem_permissionToDie))==-1){
        eCode = E_SEMAPHORE;
    }
    if (sem_destroy(&(*mutex_BoardOrderAccess))==-1){
        eCode = E_SEMAPHORE;
    }
    if (sem_destroy(&(*mutex_CisloPassengerAccess))==-1){
        eCode = E_SEMAPHORE;
    }
    if (sem_destroy(&(*mutex_File))==-1){
        eCode = E_SEMAPHORE;
    }
    return eCode;
}

void cleanUp(){
    cleanSHM();
    cleanSems();
    fclose(out);
    exit(1);
}

int main(int argc, char *argv[]){

    //signal handler
    signal(SIGTERM, cleanUp);
    signal(SIGINT,cleanUp);

    //otevrit soubor
    out = fopen("proj2.out","w");
    if(out == NULL){
        myError(E_FOPEN);
    }
    setbuf(out,NULL);
    //KONEC otevirani souboru

    time_t seed;
    srand((unsigned) time(&seed));//inicializace generatoru cisel s casovym seedem

    //Parametry
    if(getParams(argc, argv, &parametry) != E_SUCCESS)
        myError(E_PARAMS);
    //KONEC Parametry

    /////////////Inicializace sdilene pameti
    int eCode = E_SUCCESS;
    /////shm_CisloAkce
    if ((shmid_CisloAkce=shmget(IPC_PRIVATE, sizeof(int), IPC_CREAT | 0666))==-1) {
        eCode = E_SHM;
    }
    if((shm_CisloAkce=(int*)shmat(shmid_CisloAkce, NULL, 0)) == (void *) -1){
        eCode = E_SHM;
    }
    *shm_CisloAkce = 0;
    /////KONEC shm_CisloAkce

    /////shm_CisloPassenger
    if ((shmid_CisloPassenger=shmget(IPC_PRIVATE, sizeof(int), IPC_CREAT | 0666))==-1) {
        eCode = E_SHM;
    }
    if((shm_CisloPassenger=(int*)shmat(shmid_CisloPassenger, NULL, 0)) == (void *) -1){
        eCode = E_SHM;
    }
    *shm_CisloPassenger = 0;
    /////KONEC shm_CisloPassenger

    /////shm_boardOrder
    if ((shmid_BoardOrder=shmget(IPC_PRIVATE, sizeof(int), IPC_CREAT | 0666))==-1) {
        eCode = E_SHM;
    }
    if((shm_BoardOrder=(int*)shmat(shmid_BoardOrder, NULL, 0)) == (void *) -1){
        eCode = E_SHM;
    }
    *shm_BoardOrder = 0;
    /////KONEC shm_boardOrder
    if(eCode != E_SUCCESS) {
        cleanSHM();
        myError(E_SHM);
    }
    ///////////KONEC Inicializace sdilene pameti

    ////////////////Semafory Inicializace
    eCode = E_SUCCESS;
    /////sem_AllAboard
    sem_allAboard = (sem_t *)mmap(0, sizeof(sem_t), PROT_READ | PROT_WRITE,MAP_ANON | MAP_SHARED, -1, 0);
    if((sem_init(sem_allAboard, 1, 0))==-1){
        eCode = E_SEMAPHORE;
    }
    /////KONEC sem_allAboard

    /////sem_allAshore
    sem_allAshore = (sem_t *)mmap(0, sizeof(sem_t), PROT_READ | PROT_WRITE,MAP_ANON | MAP_SHARED, -1, 0);
    if((sem_init(sem_allAshore, 1, 0))==-1){
        eCode = E_SEMAPHORE;
    }
    /////KONEC sem_allAshore

    /////sem_load
    sem_load = (sem_t *)mmap(0, sizeof(sem_t), PROT_READ | PROT_WRITE,MAP_ANON | MAP_SHARED, -1, 0);
    if((sem_init(sem_load, 1, 0))==-1){
        eCode = E_SEMAPHORE;
    }
    /////KONEC sem_load

    /////sem_unload
    sem_unload = (sem_t *)mmap(0, sizeof(sem_t), PROT_READ | PROT_WRITE,MAP_ANON | MAP_SHARED, -1, 0);
    if((sem_init(sem_unload, 1, 0))==-1){
        eCode = E_SEMAPHORE;
    }
    /////KONEC sem_unload

    /////sem_permissionToDie
    sem_permissionToDie = (sem_t *)mmap(0, sizeof(sem_t), PROT_READ | PROT_WRITE,MAP_ANON | MAP_SHARED, -1, 0);
    if((sem_init(sem_permissionToDie, 1, 0))==-1){
        eCode = E_SEMAPHORE;
    }
    /////KONEC sem_permissionToDie

    /////mutex_BoardOrderAccess
    mutex_BoardOrderAccess = (sem_t *)mmap(0, sizeof(sem_t), PROT_READ | PROT_WRITE,MAP_ANON | MAP_SHARED, -1, 0);
    if((sem_init(mutex_BoardOrderAccess, 1, 1))==-1) {
        eCode = E_SEMAPHORE;
    }
    /////KONEC mutex_BoardOrderAccess

    /////mutex_CisloPassengerAccess
    mutex_CisloPassengerAccess = (sem_t *)mmap(0, sizeof(sem_t), PROT_READ | PROT_WRITE,MAP_ANON | MAP_SHARED, -1, 0);
    if((sem_init(mutex_CisloPassengerAccess, 1, 1))==-1) {
        eCode = E_SEMAPHORE;
    }
    /////KONEC mutex_CisloPassengerAccess

    /////mutex_File
    mutex_File = (sem_t *)mmap(0, sizeof(sem_t), PROT_READ | PROT_WRITE,MAP_ANON | MAP_SHARED, -1, 0);
    if((sem_init(mutex_File, 1, 1))==-1) {
        eCode = E_SEMAPHORE;
    }
    /////KONEC mutex_File
    if(eCode != E_SUCCESS) {
        cleanSHM();
        cleanSems();
        myError(E_SEMAPHORE);
    }
    ///////////////KONEC Inicializace semaforu

    //vytvorim vozicek
    pidCar = fork();
    if(pidCar < 0)//fork error
    {
        cleanSHM();
        cleanSems();
        myError(E_FORK);
    }
    else if(pidCar == 0) {//kod pro potomka
        car();
    }
    else {// kod pro puvodni proces
        //vytvorit proces na tvorbu zakazniku
        pidPCreator = fork();
        if(pidPCreator < 0) // fork error
        {
            kill(pidCar, SIGKILL);
            cleanSHM();
            cleanSems();
            myError(E_FORK);
        }
        else if(pidPCreator == 0) {//kod pro potomka
            passengerCreator();
        }
        else {// kod pro puvodni proces

        }
    }
    waitpid(pidCar,NULL,0);
    waitpid(pidPCreator,NULL,0);
    cleanSHM();
    cleanSems();
    fclose(out);
    exit(EXIT_SUCCESS);
}