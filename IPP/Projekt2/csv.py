# Author: Martin Bobcik, xbobci00
# IPP Project 2 - CSV

import sys
import argparse
import re
import os.path
from xml.etree import ElementTree as ET
from xml.dom import minidom
import io
import _csv


def main(argv):

    #nastaveni parseru argumentu
    parser = argparse.ArgumentParser(prog='csv', add_help=False)
    parser.add_argument("--help", action="store_true", dest="help", default=False)
    parser.add_argument("--input", action="store", dest="input", default=False)
    parser.add_argument("--output", action="store", dest="output", metavar='out-file', type=argparse.FileType('wt'), default=False)
    parser.add_argument("-n", action="store_true", dest="n", default=False)
    parser.add_argument("-r", action="store", dest="r", default=False)
    parser.add_argument("-s", action="store", dest="s", default=False)
    parser.add_argument("-h", nargs='?', action="store", dest="h", default=False)
    # bez -h = false
    #     -h = None
    # -h=foo = foo
    parser.add_argument("-c", action="store", dest="c", default=False)
    parser.add_argument("-l", action="store", dest="l", default=False)
    parser.add_argument("-i", action="store_true", dest="i", default=False)
    parser.add_argument("--start", action="store", dest="start", type=int, default=False)
    parser.add_argument("-e", "--error-recovery", action="store_true", dest="e", default=False)
    parser.add_argument("--missing-field", action="store", dest="missingField", default=False)
    parser.add_argument("--all-columns", action="store_true", dest="allColumns", default=False)
    parser.add_argument("--padding", action="store_true", dest="padding", default=False)

    # parsovani argumentu
    results = None
    try:
        results = parser.parse_args()
    except SystemExit:  # odchytava zakladni chyby v prepinacich
        sys.exit(1)

    # vypis napovedy
    if results.help:
        for arg in vars(results):
            if arg != 'help':
                if getattr(results, arg) is not False:
                    sys.exit(1)
        helpPrint()
        sys.exit(0)

    # osetreni spatnych argumentu scriptu
    if results.l is False and results.i is not False:
        sys.exit(1)
    if results.l is False and results.i is False and results.start is not False:
        sys.exit(1)
    if results.e is False and results.allColumns is not False:
        sys.exit(1)

    if results.start is False:
        results.start = 1

    # nastaveni implicitnich hodnot prepinacu
    if results.s is False:
        results.s = ","
    if results.s == "TAB":
        results.s = "\t"
    if len(results.s) != 1:
        sys.exit(1)
    if results.h is None:
        results.h = '-'
    if results.c is False:
        results.c = "col"
    if results.l is False:
        results.l = "row"
 #   if results.missingField is not False:
   #     results.missingField = escapeInvalidXMLCharsInField(results.missingField)
  #  else:
    if results.missingField is False:
        results.missingField = ""
    if results.start is False:
        results.start = 1


    # osetreni chyb s navratovym kodem 30
    if results.c is not False:
        if not isValidXMLTag(results.c):
            sys.exit(30)
    if results.l is not False:
        if not isValidXMLTag(results.c):
            sys.exit(30)
    if results.r is not False:
        if not isValidXMLTag(results.r):
            sys.exit(30)

    # otevreni vstupu
    if results.input is not False:
        try:
            if os.path.isfile(results.input):
                inputFile = io.open(results.input, 'r', encoding='utf8')
                #with io.open(results.input, 'r', encoding='utf8') as f:
                #    content = f.read()
                #fileHandle = io.open(results.input, 'r', encoding='utf8')
            else:
                sys.exit(3)
        except SystemExit:
            sys.exit(3)
    else:
        inputFile = sys.stdin
        # nacte vstup
        #content = inputFile.read()

    #  rozdeli po radcich
    #lines = divideByLines(content)

    # rozparsuje CSV soubor
    linesArray = getCSVStructure(inputFile, results.s)
    #for line in lines:
    #    linesArray.append(divideByCols(line, results.s))
    # v linesArray uz je nyni cely soubor rozdeleny na radky a sloupce

    # pokud je soubor prazny, vypisu hlavicku xml souboru a ukoncim ..... t
    if not linesArray:
        if results.n is False:
            if results.output is not False:
                results.output.write("<?xml version=\"1.0\" encoding=\"utf-8\"?>")
                results.output.close()
            else:
                print("<?xml version=\"1.0\" encoding=\"utf-8\"?>")

        sys.exit(0)

    # osetruje prepinac -h
    if results.h is not False:
        linesArray = substituteInvalidTagCharsInFirstLine(linesArray, results.h)

    # escapuje xml nepripustne znaky
    # linesArray = escapeInvalidXMLCharsInFile(linesArray)  # dosud zatim gut
    # tak tohle cele resi etree -_- jsem se s tim nemusel otravovat

    if results.e is False:  # pokud neni nastaveno error recovery
        for line in linesArray:  # mely by byt vsechny radky stejne dlouhe
            if len(line) != len(linesArray[0]):  # tedy delka prvniho radku je stejna jako ostatni
                sys.exit(32)

    numberOfLines = len(linesArray)
    numberOfColumns = len(linesArray[0])

    #nastaveni root Elementu v zavislosti na -r
    if results.r is not False:
        root = ET.Element(results.r)
    else:
        root = ET.Element("toDelete")
    #nastaveni prvniho radku v zavislosti na -h
    if results.h is False:
        startCol = 0
    else:
        startCol = 1

    # sestaveni vystupu
    if results.e is False:              # bez zotaveni z chyb
        for i in range(startCol, numberOfLines):
            rowTag = results.l
            #osetreni parametru -i a --padding
            if results.i is True:  # sestaveni jmena elementu pro zaznam
                if results.padding is True:
                    index = padding(results.start + numberOfLines, results.start + i)

                else:
                    index = str(results.start + i)
                row = ET.Element(rowTag)
                row.set("index", index)

            else:
                row = ET.Element(rowTag)

            for j in range(0, numberOfColumns):
                # -h se bude doplnovat tu
                if results.h is False: # argument --padding a -h
                    if results.padding is True:  #sestaveni jmena elementu pro bunku
                        colTag = results.c + padding(numberOfColumns, j+1)
                    else:
                        colTag = results.c + str(j + 1)
                else:
                    colTag = linesArray[0][j]
                column = ET.Element(colTag) # pridani elementu

                column.text = linesArray[i][j] # text elementu

                row.append(column) # pridani elementu do stromu
            root.append(row)
    else:                           # zotaveni z chyb
        for i in range(startCol, numberOfLines): # to same jako nahore, jen pro zotaveni z chyb
            rowTag = results.l                   # tj. zmena koncu iteraci
            if results.i is True:
                if results.padding is True:
                    index = padding(results.start + numberOfLines, results.start + i)

                else:
                    index = str(results.start + i)
                row = ET.Element(rowTag)
                row.set("index", index)
            else:
                row = ET.Element(rowTag)

            if results.allColumns is True:  #osetreni prepinace --all-columns
                if len(linesArray[i]) > len(linesArray[0]):
                    numberOfColumns = len(linesArray[i])
                else:
                    numberOfColumns = len(linesArray[0])

            for j in range(0, numberOfColumns):
                if results.h is False:
                    if results.padding is True:
                        colTag = results.c + padding(numberOfColumns, j+1)
                    else:
                        colTag = results.c + str(j + 1)
                else:
                    if j < len(linesArray[0]):
                        colTag = linesArray[0][j]
                    else:
                        if results.padding is True:
                            colTag = results.c + padding(numberOfColumns, j + 1)
                        else:
                            colTag = results.c + str(j + 1)
                column = ET.Element(colTag)
                if j < len(linesArray[i]):
                    column.text = linesArray[i][j]
                else:
                    column.text = results.missingField

                row.append(column)
            root.append(row)

    # tisk vystupu plus orezani nepotrebnych radku podle prepinacu

    resultString = prettify(root)
    if results.r is False:
        #orezani root elementu
        resultString = cutXMLRootElem(resultString)
    if results.n is True:
        # oriznuti xml hlavicky
        try:  # tady to nekdz dela problemy...nekdz to je string nekdy bytes
            resultString = cutXMLHeader(resultString)
        except:
            resultString = cutXMLHeader(resultString.decode("UTF-8"))

    try: # tady to nekdy dela problemy...nekdy to je string nekdy bytes
        resultString = resultString.decode("UTF-8")
    except:
        ...

    if results.output is not False:
        results.output.write(resultString)
        results.output.close()
    else:
        try:  # tady to nekdy dela problemy...nekdy to je string nekdy bytes
            print(resultString.decode("UTF-8"))
        except:
            print(resultString)

#tisk napovedy
def helpPrint():
    print("Skript pro konverzi formátu CSV do XML podle zadaných kritérií.\n")
    print("--help     \tZobrazí tuto nápovědu")
    print("--input    \tNastaví vstupní soubor, pokud tento přepínač chybí, za vstup se považuje standardní vstup")
    print("--output   \tNastaví výstupní soubor, pokud tento přepínač chybí, výstup je přesměrován na standardní výstup")
    print("-n         \tSkript nevygeneruje XML hlavičku")
    print("-r=root    \tObalí výsledná XML strom elementem root. Pokud není přepínač zadán, výsledek nemá kořenový element")
    print("-s=sep     \tNastaví znak oddělující jednotlivé buňky CSV souboru")
    print("-h=[subst] \tPrvní záznam CSV souboru je brán jako hlavička, podle které se odvozují jména XML elementů.")
    print("           \tNeplatné znaky jsou nahrazeny subst. Implicitně \"-\"")
    print("-c=col     \tUrčuje prefix elementu colX, označující nepojmenované sloupce, kde X je čítač elementů. Implicitně col")
    print("-l=line    \tUrčuje jméno elementu obalující každý řádek CSV. Implicitně row")
    print("-i         \tVloží atribut index s číselnou hodnotou do elementu line. Pouze v kombinaci s -l")
    print("--start=n  \tInicializace čítače pro parametr -i na zadané číslo n. Implicitně 1. Pouze v kombinaci s -i\n")
    print("-e,                \tZotavení z chybného počtu sloupců v neprvním řádku CSV souboru. ")
    print("--error-recovery   \tKaždý sloupec je doplněn prázdným řetězcem. Přebívající sloupce jsou vynechány.\n")
    print("--missing-field=val\tPrázdné sloupce doplněny hodnotou val. Pouze v kombinaci s -e, --error-recovery\n")
    print("--all-columns      \tSloupce, které jsou v chybném CSV navíc nejsou ignorovány, ale také vytištěny.")
    print("                   \tPouze v kombinaci s -e, --error-recovery.")

#osetreni prepinace padding
#max = nejvyssi hodnota citace
#actual = aktualni hodnota citace
#vrati string citace
def padding(max, actual):
    if len(str(max)) == len(str(actual)):
        return str(actual)

    result = str(actual)
    while len(result) < len(str(max)):
        result = "0" + result

    return result


#rozparsovani vsupniho csv souboru
#separator = oddelovac bunek csv souboru
#fileHandle = odkaz na otevrity csv soubor
#vrati dvojrozmerny seznam bunek
def getCSVStructure(fileHandle, separator):
    reader = _csv.reader(fileHandle, delimiter=separator)
    result = []
    for row in reader:
        result.append(row)
    return result


#oreze root element vysledneho XML souboru
#inputString = XML soubor
# vrati string xml souboru bez root elementu
def cutXMLRootElem(inputString):
    inputToList = inputString.splitlines()  # prevede vstupni string na list podle radku
    del inputToList[1]  # odsekne druhy a posledni radek (root element)
    del inputToList[-1]
    result = ""
    for line in inputToList:  # prevede list zpet na string
        tmpline = line.decode("UTF-8") + "\n"
        if line != inputToList[0]:  # uz jsem zapomel , co jsem timto postupem sledoval...priste se polepsim
            result += tmpline[1:]
        else:
            result += tmpline
    return result

#odstrani XML header 
#inputString = XML soubor
# vrati string xml souboru bez headeru
def cutXMLHeader(inputString):
    inputToList = inputString.splitlines()
    del inputToList[0]
    result = ""
    for line in inputToList:
        result += line + "\n"
    return result


# vola se v pripade zadani prepinace -h
# nahrazuje spatne znaky znakem v subst, a testuje validitu vysledneho xml tagu
#lines = dvojrozmerny seznam bunek
#vrati dvojrozmerny seznam bunek
def substituteInvalidTagCharsInFirstLine(lines, subst):
    i = 0
    while i < len(lines[0]):
        lines[0][i] = re.sub(r"\n", 2*subst, lines[0][i])  # "\n" je bran jako dva znaky, tak ho nahradim 2krat
        lines[0][i] = re.sub(r"[^\w_ěščřžýáíéúůťňďó]", subst, lines[0][i])  # nahradi neplatne znaky subst
        if not isValidXMLTag(lines[0][i]):  # pote zkontroluje validitu tagu
            sys.exit(31)
        i += 1
    return lines


# zjisti, jestli je dany XML Tag validni
def isValidXMLTag(tag):
    reXMLTag = r"^[_a-zA-ěščřžýáíéúůťňďó][\w._-]*(?=$)"
    p = re.compile(reXMLTag, re.MULTILINE | re.UNICODE)  # matchne tag podle zadaneho vyrazu
    if p.match(tag):  # pokud neco najde, je to true
        return True
    return False


# nahradi nepovolene XML znaky zadane bunky escapovacimi ekvivalenty
def escapeInvalidXMLCharsInField(field):
    field = field.replace("&", "&amp;")  # tohle asi umi knihovna etree
    field = field.replace("<", "&lt;")
    field = field.replace(">", "&gt;")
    field = field.replace("\"", "&quot;")
    field = field.replace("'", "&apos;")
    return field


# projde cely soubor, a nahradi neplatne xml znaky
def escapeInvalidXMLCharsInFile(lines):
    i = 0
    while i < len(lines):
        j = 0
        while j < len(lines[i]):
            lines[i][j] = escapeInvalidXMLCharsInField(lines[i][j])
            j += 1
        i += 1
    return lines


# rozdeli radek po bunkach, bunky jsou oddeleny separatorem
def divideByCols(line, separator=','):
    reRows = r"(?:%s?(?:\"{1,2})(.+?)(?:\"{1,2})(?=%s|$))|(?:%s?(.+?)(?=%s|$))" % (separator, separator, separator, separator)
    p = re.compile(reRows, re.MULTILINE | re.UNICODE)
    tmp = p.findall(line)
    result = []
    for row in tmp:  # ulozi bud prvni nebo druhy matchGroup (kvuli strukture vyrazu
        if row[0]:
            result.append(row[0].replace("\"\"", "\""))
        else:
            result.append(row[1])

    return result  # list of rows on one line


# rozdeli celi soubor po radcich ... mozna bz to slo i bez regexu
def divideByLines(content):
    reLines = r"(.+)"
    p = re.compile(reLines, re.MULTILINE | re.UNICODE)
    return p.findall(content)  # list of lines

# naformatuje vystupni string
def prettify(elem):
    rough_string = ET.tostring(elem, 'utf-8')  # prevede strom na retezec
    reparsed = minidom.parseString(rough_string)  # rozparsuje na XML
    return reparsed.toprettyxml(encoding="UTF-8", indent="\t")  # ulozi zpet jako naformatovany string

if __name__ == '__main__':
    main(sys.argv[1:])