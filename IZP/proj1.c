/*
 *  Martin Bobčík
 *  xbobci00
 *  03.11.2015
 *  VUT FIT v Brne - 1BIA
 */

/*
 *Implementujte program pro jednoduche zpracovani slov v textu.
 *Program bude implementovat funkce pro
 *detekci cisel,
 *detekci kalendarniho data,
 *test na prvocislo a
 *detekci palindromu.
 *Vstupnim textem je standardni vstup (stdin).
 *Vstupni soubor se zpracovava po slovech podle definice konverzniho specifikatoru %s funkce scanf.
 */

// https://wis.fit.vutbr.cz/FIT/st/cwk.php.cs?title=IZP:Projekt1&csid=599103&id=10361

#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include <stdbool.h>

/**
*Funkce, ktera zjisti delku pole znaku
*@param array - pole znaku, jehoz delku chceme znat
*@return i - pozadovana delka pole znaku
*/
int myStrlen(const char word[]) //ready
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
* funkce vrati retezec obsahujici nazev dne v tydnu
*@param year - rok
*@param month - mesic
*@param day - den
*@return pole znaku, den v tydnu
*/
char * WeekDay(const int year, const int month, const int day) // ready
{
    //struktura tm, pouziti pri operacich nad casem a datem
    struct tm date;

    //do struktury ulozim datum
    date.tm_year = year - 1900;
    date.tm_mon = month - 1;
    date.tm_mday = day;
    date.tm_sec = 1;
    date.tm_hour = 1;
    date.tm_min = 1;
    date.tm_isdst = -1;
    //prevede datum
    if(mktime(&date)== 1)
    {
        //pokud se to nepodari, vyhodi chybu
        static char result[20] = "-error-";
        return result;
    }
    else
    {
        //jinak zapise den v tydnu a ukonci metodu
        //printf("%d %d %d/n", date.tm_year,date.tm_mon,date.tm_mday);
        static char result[20];
        //%a - zapise den v tydnu skratkou
        //%A - zapise cely nazev dne
        strftime(result, sizeof(result),"%a",&date);
        return result;
    }
}

/**
*Funkce provede test, zda je cislo prvocislo
*@param testNumber - testovane cislo
*@return pravdivostni hodnota prvociselnosti cisla
*/
bool isPrime(const int testNumber) // ready
{
    //otestuje, jestli je cislo <= 1 =>neni prvnocislo
    if(testNumber <= 1)
        return false;
    //pokud je cislo dva, je prvocislo
    if(testNumber == 2)
        return true;
    //jestli je delitelne dvema, neni prvocislo
    if(testNumber % 2 == 0)
        return false;
    //pokracuje pro dalsi cisla
    int i;
    for(i = 3; i < testNumber / 2; i += 2)
    {
        if(testNumber % i == 0)
            return false;
    }
    return true;
}

/**
* Provede test slova na palindrom
*@param word - pole znaku slova
*@return pravdivostni hodnota palindromu slova
*/
bool isPalindrom(const char word[]) // ready
{
    //nactu délku slova
    int charCount = myStrlen(word);
    //inicializuje a deklaruje pomocnou promennou pro cyklus
    int i;
    //projde pole znaku, a prorovna znak po znaku s jeho protejskem od konce
    for(i = 0; i < charCount / 2; i++)
    {
        if(word[i] != word[charCount - i - 1])
        {
            //pokud se znaky nerovnaji, vrati false
            return false;
        }
    }
    //pokud se vsechny znaky rovnaji, vrati true
    return true;
}

/**
* Funkce porovna dve pole znaku. Pokud se rovnaji, vrati 1, jinak vrati 0.
*@param string1 - prvni pole znaku
*@param string2 - druhe pole znaku
*@return pravdivostni hodnota rovnosti dvou poli znaku
*/
bool myStrcmp(const char string1[],const char string2[]) //ready
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
* Funkce zjisti, zda je na vstupu cele kladne cislo
* @param number - retezec k otestovani
* @return pravdivostni hodnota, cele kladne cislo == true, jinak false
*/
bool isNumber(const char number[]) //ready
{
    // projde retezec znak po znaku
    int i;
    for(i = 0; i < myStrlen(number); i++)
    {
        //pokud znak neni cislo, vrati false
        if(!(number[i] >= 48 && number[i] <= 57))
        {
            return false;
        }
    }
    return true;
}

/**
* Funkce zjisti, zda ma zadany retezec format data, metoda netestuje
*  jestli jsou zadany mesice a dny korektne
* @param word - retezec k otestovani
* @return pravdivostni hodnota
*/
bool isDate(const char word[]) //ready
{
    // DDDD-DD-DD => 10 znaku
    if(myStrlen(word) != 10) //pokud retezec nema stejny pocet znaku, neni to datum
        return false;

    //pomocna promena do ktere budu ukladat cisla
    char tmp[9];
    // pomocna promena na pocitani indexu v zadanem retezci
    int indexWord;
    //pomocna promena na pocitani indexu v predchozi pomocne promene
    int indexTmp = 0;
    //projdu zadany retezec znak po znaku
    for(indexWord = 0; indexWord < myStrlen(word); indexWord++)
    {
        // na indexu 4 a 7 maji byt  pomlcky
        if(indexWord != 4 && indexWord != 7)
        {
            //ulozim vsechny ostatni znaky do pomocne promene
            tmp[indexTmp] = word[indexWord];
            //a inkrementuji jeji index
            indexTmp++;
        }
        else if(word[indexWord] != '-') // pokud na miste 4 nebo 7 neni pomlcka vrati false
            return false;
    }
    tmp[8] = '\0'; // na konec pomocne promene ulozim zarazku
    // pokud jsou v pomocne promene poze cisla, zadany retezec ma format data
    if(isNumber(tmp))
        return true;
    return false;
}

/**
* Funkce ziska hodnoty dnu, mesicu a let z data
* @param word - retezec znaku, z  ktereho chceme ziskat hodnoty
* @param numberOfChars - pocet znaku ktere chceme ziskat
* @param firstChar - index pocatecniho znaku
* @return cislo ziskane ze zadaneho retezce odpovidajicim zpusobem
*/
int dateTrimmer(const char word[], const int numberOfCHars, int firstChar)//ready
{
    // deklaruji  promennou do ktere ulozim vysledek
    char result[numberOfCHars];
    // deklaruju a inicializuji pomocnou promennou pro pocitani indexu vysledku
    int indexResult = 0;
    // deklaruji pomocnou promennou pro pocitani indexu zadaneho retezce
    int indexWord;
    // projdu retezec od prvniho zadaneho indexu po posledni zadany
    for(indexWord = firstChar; indexWord < firstChar + numberOfCHars; indexWord++)
    {
        //kazdy znak ulozim do vysledku
        result[indexResult] = word[indexWord];
        // inkrementuji index vysledku
        indexResult++;
    }
    // vratim vysledek jako int32
    return atoi(result);
}

/**
* Funkce zjisti zda byl zadan spravny argument,
*  a vrati odpovidajici retezec
* @param formula - retezec, jenz zapricini vypsani napovedy
* @param argv - argument napovedy
* @param help - text napovedy
* @return retezec s napovedou, nebo s chybovym hlasenim
*/
char * argumentHelp(char formula[], char argv[],char help[])
{
    //osetreni pozadavku na vypsani napovedy
    //pokud se argument bude rovnat

    if(myStrcmp(argv, formula))
    {
        //pak vypise napovedu
        return help;
    }
    else
    {
        //jinak vrati chybovou hlasku
        return "Invalid argument\n";
    }
}

/**
* Procedura zjisti, zda je zadano datum, cislo, nebo slovo
*  a vytiskne pozadovany vystup
* @param wordCurrent - vstupni retezec znaku
*/
void inputScanner(const char wordCurrent[])
{
    // pokud ma retezec format data
    if(isDate(wordCurrent))
    {
        // ulozi zkratku dne pomoci metody Weekday, jejiz argumenty preda metoda dateTrimmer
        // ktera z retezce vyextrahuje hodnoty dnu, mesicu a let
        char * day = WeekDay(dateTrimmer(wordCurrent, 4, 0),dateTrimmer(wordCurrent, 2, 5),dateTrimmer(wordCurrent, 2, 8));
        //vytiskne vysledek
        printf("date: %s %s\n", day, wordCurrent);
    }
    // pokud jsou v retezci sama cisla
    else if(isNumber(wordCurrent))
    {
        // vytiskne cislo jako retezec
        printf("number: %s", wordCurrent);

        //prevede retezec na int32, pokud je cislo vetsi nez int_max, vrati -1
        int number = atoi(wordCurrent);
        // pokud se povedl prevod
        if(number != -1)
        {
            // provede test na prvocislo
            if(isPrime(number))
                printf(" (prime)");
        }
        // odradkuje
        printf("\n");
    }
    // pokud retezec neni ani datum ani cislo je to "slovo"
    else
    {
        // vytiskne slovo
        printf("word: %s ", wordCurrent);

        //provede test na palindrom, vytiskne vysledek a odradkuje
        if(isPalindrom(wordCurrent))
            printf("(palindrom)\n");
        else
            printf("\n");
    }
}

int main(int argc, char *argv[])
{
    //osetreni pozadavku na vypsani napovedy
    //pokud dostane program argument
    if(argc>=2)
    {
        //text napovedy  deklarace na vice radku pomoci "\"
        char helpVal[] = "Program zpracuje retezce na standartnim vstupu oddelena bilymy znaky.\n\
U kazdeho retezce zjisti, zda je to cislo, datum, nebo slovo.\n\n\
Pokud identifikuje kladne, cele cislo, zjisti, zda je take prvocislem.\n\
Pokud identifikuje datum ve formatu YYYY-MM-DD, zjisti jaky den v tydnu to je.\n\
Pokud neni retezec ani cislo, ani datum, je identifikovan jako slovo.\n\
U slov zjisti, zda je to palindrom.\n\
Nad kazdym retezcem, provede urcenou operaci, vypise jeho zarazeni,\nretezec, a vlastnost.\n\n\
Autor programu: Martin Bobcik xbobci00\n1. projekt do IZP\n";

        printf(argumentHelp("--help", argv[1], helpVal));
        return 0;
    }

    //deklarace a nasledna inicializace promenne, do ktere se budou ukladat slova
    //maximalni delka slova je 100 znaku, plus jeden jako zarazka
    char wordCurrent[101];
    //cyklus probiha, dokud program nacte slovo
    int readResult;             // kdyz ma slovo vic jak 100 znaku, nacte prvnich 100 znaku, a pak zbytek
    while((readResult = scanf("%100s", wordCurrent)) == 1)
    {
        // nad kazdym retezcem zavola metodu, ktera vykona pozadovane ukoly
        inputScanner(wordCurrent);
    }
    return 0;
}
