	
/* c206.c **********************************************************}
{* Téma: Dvousměrně vázaný lineární seznam
**
**                   Návrh a referenční implementace: Bohuslav Křena, říjen 2001
**                            Přepracované do jazyka C: Martin Tuček, říjen 2004
**                                            Úpravy: Bohuslav Křena, říjen 2016
**
** Implementujte abstraktní datový typ dvousměrně vázaný lineární seznam.
** Užitečným obsahem prvku seznamu je hodnota typu int.
** Seznam bude jako datová abstrakce reprezentován proměnnou
** typu tDLList (DL znamená Double-Linked a slouží pro odlišení
** jmen konstant, typů a funkcí od jmen u jednosměrně vázaného lineárního
** seznamu). Definici konstant a typů naleznete v hlavičkovém souboru c206.h.
**
** Vaším úkolem je implementovat následující operace, které spolu
** s výše uvedenou datovou částí abstrakce tvoří abstraktní datový typ
** obousměrně vázaný lineární seznam:
**
**      DLInitList ...... inicializace seznamu před prvním použitím,
**      DLDisposeList ... zrušení všech prvků seznamu,
**      DLInsertFirst ... vložení prvku na začátek seznamu,
**      DLInsertLast .... vložení prvku na konec seznamu, 
**      DLFirst ......... nastavení aktivity na první prvek,
**      DLLast .......... nastavení aktivity na poslední prvek, 
**      DLCopyFirst ..... vrací hodnotu prvního prvku,
**      DLCopyLast ...... vrací hodnotu posledního prvku, 
**      DLDeleteFirst ... zruší první prvek seznamu,
**      DLDeleteLast .... zruší poslední prvek seznamu, 
**      DLPostDelete .... ruší prvek za aktivním prvkem,
**      DLPreDelete ..... ruší prvek před aktivním prvkem, 
**      DLPostInsert .... vloží nový prvek za aktivní prvek seznamu,
**      DLPreInsert ..... vloží nový prvek před aktivní prvek seznamu,
**      DLCopy .......... vrací hodnotu aktivního prvku,
**      DLActualize ..... přepíše obsah aktivního prvku novou hodnotou,
**      DLSucc .......... posune aktivitu na další prvek seznamu,
**      DLPred .......... posune aktivitu na předchozí prvek seznamu, 
**      DLActive ........ zjišťuje aktivitu seznamu.
**
** Při implementaci jednotlivých funkcí nevolejte žádnou z funkcí
** implementovaných v rámci tohoto příkladu, není-li u funkce
** explicitně uvedeno něco jiného.
**
** Nemusíte ošetřovat situaci, kdy místo legálního ukazatele na seznam 
** předá někdo jako parametr hodnotu NULL.
**
** Svou implementaci vhodně komentujte!
**
** Terminologická poznámka: Jazyk C nepoužívá pojem procedura.
** Proto zde používáme pojem funkce i pro operace, které by byly
** v algoritmickém jazyce Pascalovského typu implemenovány jako
** procedury (v jazyce C procedurám odpovídají funkce vracející typ void).
**/

#include "c206.h"

int solved;
int errflg;

void DLError() {
/*
** Vytiskne upozornění na to, že došlo k chybě.
** Tato funkce bude volána z některých dále implementovaných operací.
**/	
    printf ("*ERROR* The program has performed an illegal operation.\n");
    errflg = TRUE;             /* globální proměnná -- příznak ošetření chyby */
    return;
}

void DLInitList (tDLList *L) {
/*
** Provede inicializaci seznamu L před jeho prvním použitím (tzn. žádná
** z následujících funkcí nebude volána nad neinicializovaným seznamem).
** Tato inicializace se nikdy nebude provádět nad již inicializovaným
** seznamem, a proto tuto možnost neošetřujte. Vždy předpokládejte,
** že neinicializované proměnné mají nedefinovanou hodnotu.
**/
    //NULLuji vsechny ukazatele
 L->Act = L->First = L->Last = NULL;

}

void DLDisposeList (tDLList *L) {
/*
** Zruší všechny prvky seznamu L a uvede seznam do stavu, v jakém
** se nacházel po inicializaci. Rušené prvky seznamu budou korektně
** uvolněny voláním operace free. 
**/
 //deaktivuji seznam a zrusim ukazatel na posledni prvek
 L->Act = L->Last = NULL;
 //dokud neni seznam prazdny
 while(L->First != NULL){
  //ulozim si ruseny prvek
  tDLElemPtr del = L->First;
  // posunu ukazatel na prvni prvek, abych nestratil ukazatel na seznam
  L->First = del->rptr;
  //dealokuji prvek na ulozene pozici
  free(del);
 }

}

void DLInsertFirst (tDLList *L, int val) {
/*
** Vloží nový prvek na začátek seznamu L.
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/

 //alokuji pamet pro prvek
 tDLElemPtr newElem = malloc(sizeof(struct tDLElem));
 //kontrola alokace
 if(newElem ==NULL)
  DLError();
 else{
  //zapisu data do prvku
  newElem->data = val;
  //prvek vlevo od tvoreneho je logicny NULL(na levo od prvniho nic neni)
  newElem->lptr = NULL;
  //pokud je tvoreny prvek prvni vytvoreny v seznamu, tak je i pravy ukazatel NULL
  if(L->First == NULL){
   newElem->rptr = NULL;
   //a zaroven je tvoreny prvek posledni
   L->Last = newElem;
  }else {
   //z prvniho prvku udelam druhy
   L->First->lptr = newElem;
   newElem->rptr = L->First;
  }
  //z tvoreneho prvni
  L->First=newElem;
 }

}

void DLInsertLast(tDLList *L, int val) {
/*
** Vloží nový prvek na konec seznamu L (symetrická operace k DLInsertFirst).
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/
 //alokace pameti pro tvoreny prvek, a jeji kontrola
 tDLElemPtr newElem = malloc(sizeof(struct tDLElem));
 if(newElem ==NULL)
  DLError();
 else {
  //ulozeni dat do tvoreneho prvku
  newElem->data = val;
  //napravo od prvnu nic neni(je posledni)
  newElem->rptr = NULL;
  //pokud je tvoreny prvek prvni vytvoreny v seznamu, stane se prvnim a vyNULLuji i jeho levy ukazatel
  if(L->Last == NULL){
   newElem->lptr = NULL;
   L->First = newElem;
  }else {//z posledniho prvku udelam predposledni
   L->Last->rptr = newElem;
   newElem->lptr = L->Last;
  }//z tvoreneho prvku udelam posledni
  L->Last = newElem;
 }
}

void DLFirst (tDLList *L) {
/*
** Nastaví aktivitu na první prvek seznamu L.
** Funkci implementujte jako jediný příkaz (nepočítáme-li return),
** aniž byste testovali, zda je seznam L prázdný.
**/

 L->Act = L->First;

}

void DLLast (tDLList *L) {
/*
** Nastaví aktivitu na poslední prvek seznamu L.
** Funkci implementujte jako jediný příkaz (nepočítáme-li return),
** aniž byste testovali, zda je seznam L prázdný.
**/

 L->Act = L->Last;

}

void DLCopyFirst (tDLList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu prvního prvku seznamu L.
** Pokud je seznam L prázdný, volá funkci DLError().
**/

 if(L->First == NULL)
  DLError();
 else{
  *val = L->First->data;
 }

}

void DLCopyLast (tDLList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu posledního prvku seznamu L.
** Pokud je seznam L prázdný, volá funkci DLError().
**/

 if(L->First == NULL)
  DLError();
 else{
  *val = L->Last->data;
 }


}

void DLDeleteFirst (tDLList *L) {
/*
** Zruší první prvek seznamu L. Pokud byl první prvek aktivní, aktivita 
** se ztrácí. Pokud byl seznam L prázdný, nic se neděje.
**/
 //kontola existence ruseneho prvku
 if(L->First!= NULL){
  //ulozim ruseny prvek do pomocne promenne
  tDLElemPtr del = L->First;
  //posunu ukazatel na prvni prvek doprava
  L->First = del->rptr;
  if(L->First!= NULL) {//pokud uz neni seznam prazny, smazu ukazatel na ruseny prvek
   L->First->lptr = NULL;
  }else{ // pokud je seznam prazdny, zrusim ukazatel na posledni prvek
   L->Last = NULL;
  }
  if (L->Act == del) // pokud je ruseny prvek i aktivni prvek, zrusim aktivitu
   L->Act = NULL;
  //dealokuju ruseny prvek
  free(del);
 }

}

void DLDeleteLast (tDLList *L) {
/*
** Zruší poslední prvek seznamu L. Pokud byl poslední prvek aktivní,
** aktivita seznamu se ztrácí. Pokud byl seznam L prázdný, nic se neděje.
**/

 //kontorla existenece ruseneho prvku
 if(L->Last!=NULL){
  //ruseny prvek ulozim do pomocne promenne
  tDLElemPtr tmp = L->Last;
  //posunu ukazatel na posledni prvek do leva
  L->Last = tmp->lptr;
  //pokud neni seznam prazdny, uzemnim pravy ukazatel noveho posledniho prvku
  if (L->Last != NULL)
   L->Last->rptr = NULL;
  else// jinak zrusim referenci na prvni prvek
   L->First = NULL;
  //zrusim moznou aktivitu ruseneho prvku
  if(L->Act == tmp)
   L->Act = NULL;
  //dealokuju ruseny prvek
  free(tmp);
 }

}

void DLPostDelete (tDLList *L) {
/*
** Zruší prvek seznamu L za aktivním prvkem.
** Pokud je seznam L neaktivní nebo pokud je aktivní prvek
** posledním prvkem seznamu, nic se neděje.
**/
 //zjistim, jestli je seznam aktivni, a jestli je co rusit
 if (L->Act!=NULL && L->Act != L->Last){
  tDLElemPtr del = L->Act->rptr;//ruseny prvek ulozim do pomocne promenne
  //pokud neni ruseny prvek posleni, spojim nasledujici prvek s Aktualnim
  if(del->rptr != NULL)
   del->rptr->lptr = L->Act;
  else//jinak nastavim aktualni jako posledni
   L->Last = L->Act;
  //spojim aktualni prvek s prvkem nasledujicim po rusenem
  L->Act->rptr = del->rptr;
  //dealokuju ruseny prvek
  free(del);
 }
}

void DLPreDelete (tDLList *L) {
/*
** Zruší prvek před aktivním prvkem seznamu L .
** Pokud je seznam L neaktivní nebo pokud je aktivní prvek
** prvním prvkem seznamu, nic se neděje.
**/

 //zjistim, jestli je seznam aktivni, a jestli je co rusit
 if(L->Act != NULL && L->Act!=L->First){
  tDLElemPtr del = L->Act->lptr;//ruseny prvek ulozim do pomocne promenne
  //pokud neni ruseny prvek prvni, spojim predchozi prvek s Aktualnim
  if(del->lptr != NULL)
   del->lptr->rptr = L->Act;
  else//jinak nastavim prvni prvek na aktualni
   L->First = L->Act;
  //nastavim levy ukazatel aktualniho prvku na prvek pred rusenym
  L->Act->lptr= del->lptr;
  //dealokuju ruseny prvek
  free(del);
 }

}

void DLPostInsert (tDLList *L, int val) {
/*
** Vloží prvek za aktivní prvek seznamu L.
** Pokud nebyl seznam L aktivní, nic se neděje.
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/

 if(L->Act!=NULL) {
  //alokuju pamet pro tvoreny prvek a zkontroluji alokaci
  tDLElemPtr newElem = malloc(sizeof(struct tDLElem));
  if (newElem == NULL)
   DLError();
  else {
   //ulozim data do prvku
   newElem->data = val;
   //aktivni prvek nasleduje po aktualnim
   newElem->lptr = L->Act;
   //nasledujici prvek po aktualnim ulozim do praveho ukazatele
   newElem->rptr = L->Act->rptr;
   //tvoreny prvek ulozim do praveho ukazatele aktualniho (uz jsem si ulozil nasledujici prvek)
   L->Act->rptr = newElem;
   //pokud je tvoreny prvek posledni, nastavim na nej prislusny ukazatel
   if (L->Act == L->Last) {
    L->Last = newElem;
   } else {//jinak zapojim nasledujici prvek
    newElem->rptr->lptr = newElem;
   }
  }
 }
}

void DLPreInsert (tDLList *L, int val) {
/*
** Vloží prvek před aktivní prvek seznamu L.
** Pokud nebyl seznam L aktivní, nic se neděje.
** V případě, že není dostatek paměti pro nový prvek při operaci malloc,
** volá funkci DLError().
**/

 //alokuju pamet pro tvoreny prvek a zkontroluji alokaci
 if(L->Act!=NULL) {
  tDLElemPtr newElem = malloc(sizeof(struct tDLElem));
  if (newElem == NULL)
   DLError();
  else {
   //ulozim data do prvku
   newElem->data = val;
   //tvoreny prvek je pred aktualnim
   newElem->lptr = L->Act->lptr;
   newElem->rptr = L->Act;
   //levy ukazatel aktualniho prvku nastavim na tvoreny
   L->Act->lptr = newElem;
   // pokud je vytvoreny prvek prvni, nastavim prislusny ukazatel
   if (L->Act == L->First) {
    L->First = newElem;
   } else {//jinak zapojim predchozi prvek
    newElem->lptr->rptr = newElem;
   }
  }
 }
}

void DLCopy (tDLList *L, int *val) {
/*
** Prostřednictvím parametru val vrátí hodnotu aktivního prvku seznamu L.
** Pokud seznam L není aktivní, volá funkci DLError ().
**/

 if(L->Act == NULL)
  DLError();
 else{
  *val = L->Act->data;
 }

}

void DLActualize (tDLList *L, int val) {
/*
** Přepíše obsah aktivního prvku seznamu L.
** Pokud seznam L není aktivní, nedělá nic.
**/

 if(L->Act!=NULL)
  L->Act->data = val;

}

void DLSucc (tDLList *L) {
/*
** Posune aktivitu na následující prvek seznamu L.
** Není-li seznam aktivní, nedělá nic.
** Všimněte si, že při aktivitě na posledním prvku se seznam stane neaktivním.
**/

 if(L->Act!=NULL)
  L->Act = L->Act->rptr;

}


void DLPred (tDLList *L) {
/*
** Posune aktivitu na předchozí prvek seznamu L.
** Není-li seznam aktivní, nedělá nic.
** Všimněte si, že při aktivitě na prvním prvku se seznam stane neaktivním.
**/

 if(L->Act!=NULL)
  L->Act = L->Act->lptr;

}

int DLActive (tDLList *L) {
/*
** Je-li seznam L aktivní, vrací nenulovou hodnotu, jinak vrací 0.
** Funkci je vhodné implementovat jedním příkazem return.
**/


 return  (L->Act!=NULL);
}

/* Konec c206.c*/
