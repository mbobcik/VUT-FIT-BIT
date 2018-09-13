Popis:
Vytvořte komunikující aplikaci s pomocí BSD soketů dle níže uvedeného zadání. Projekt odevzdejte zabalený v archívu tar.gz. Součástí bude kompletní zdrojový kód, funkční Makefile a soubor s dokumentací.

Zadání
 
Napište klient/server aplikaci pro přenos souborů, který bude komunikovat pomocí HTTP a bude používat RESTful API. REST rozhraní je definováno pro následující operace: 
 
Nahrání souboru na server:
 
PUT /USER-ACCOUNT/REMOTE-PATH?type=[file|folder] HTTP/1.1
 
Stažení souboru ze serveru:
 
GET /USER-ACCOUNT/REMOTE-PATH?type=[file|folder] HTTP/1.1
 
Smazání souboru/adresáře:
 
DELETE /USER-ACCOUNT/REMOTE-PATH?type=[file|folder] HTTP/1.1
 
Požadavek musí obsahovat minimálně tyto HTTP hlavičky:
Date  - Timestamp klienta v době vytvoření požadavku.
Accept - Požadavaný typ obsahu pro odpověď 
Accept-Encoding - Podporový způsob kódování dat (identity, gzip, deflate)
Content-Type - MIME typ obsahu požadavku (pro PUT či POST)
Content-Length - Délka obsahu požadavku (pro PUT či POST)
Odpověď bude obsahovat tyto hlavičky:
Date - Timestamp serveru v době vyřízení požadavku.
Content-Type - typ obsahu odpovědi podle MIME
Content-Length - délka obsahu odpovědi 
Content-Encoding - typ kódování obsahu (identity, gzip, deflate)
Stavové kódy pro odpovědi:
200 OK - operace byla provedena úsopěšně
404 Not Found - objekt (soubor/adresář) v požadavku neexistuje
400 Bad Request - při přístupu k objektu jiného typu než uvedeného v požadaku (požadavek na operaci nad souborem, ale REMOTE-PATH ukazuje na adresář)  
Pro příadné rozšíření další podle potřeby, viz http://www.restapitutorial.com/httpstatuscodes.html.
 
Krom soketů nejsou povoleny jiné síťové knihovny. Lze použít hlavičkové soubory netinet/*, stejně jako klasické hlavičkové soubory stdio.h atp. Knihovny pro manipulaci například s JSON jsou povoleny. Použijte dostupné knihovny v testovacím prostředí.
 
Použití aplikace
 
Server se bude jmenovat ftrestd a bude spouštěn s volitelnými parametry:
 
ftrestd [-r ROOT-FOLDER] [-p PORT]
-r ROOT-FOLDER specifikuje kořenový adrssář, kde budou ukládány soubory pro jednotlivé uživatele, defaultní hodnota je aktuální 
-p PORT specifikuje port, na kterém bude server naslouchat, implicitně 6677
Klient se bude jmenovat ftrest a bude mít dva povinné parametry následované (volitelným) parameterm:
 
ftrest COMMAND REMOTE-PATH [LOCAL-PATH]
 COMMAND je příkaz 
 REMOTE-PATH je cesta k souboru nebo adresáři na serveru
 LOCAL-PATH je cesta v lokální souborovém systému, povinné pro put
Základní příkazy, které je nutné implementovat:
del smaže soubor určený REMOTE-PATH na serveru
get zkopíruje soubor z REMOTE-PATH do aktuálního lokálního adresáře či na místo určené pomocí LOCAL-PATH je-li uvedeno
put zkopíruje soubor z LOCAL-PATH do adresáře REMOTE-PATH 
lst  vypíše obsah vzdáleného adresáře na standardní výstup (formát bude stejný jako výstup z příkazu ls)
mkd vytvoří adresář specifikovaný v REMOTE-PATH na serveru
rmd odstraní adresář specifikovaný V REMOTE-PATH ze serveru
Uvedené operace mohou skončit s chybou, která se vždy vypíše do stderr:
 "Not a directory." když REMOTE-PATH ukazuje na soubor, ale je použita operace lst, rmd
 "Directory not found." když REMOTE-PATH neukazuje na žádny existující objekt při použití operace lst, rmd
 "Directory not empty." když REMOTE-PATH ukazuje na adresář, který není prázdný a je použita operace rmdir
 "Already exists." když REMOTE-PATH ukazuje na adresář/soubor, který již existuje a je použita operace mkd či put.
 "Not a file." když REMOTE-PATH ukazuje na adresář, ale je použita operace del, get.
 "File not found." když REMOTE-PATH neukazuje na žádny existující objekt při použití operace del, get
"User Account Not Found" pokud je operace nad neexistujícím uživatelem.
 "Unknown error." pro ostatní chyby.
Příklady
 
Vytvoření adresáře bar na serveru bežícím na lokálním počítači a portu 12345:
$ ftrest mkd http://localhost:12345/tonda/foo/bar
 
Nahrání souboru doc.pdf na serveru do adresáře bar: 
$ ftrest put http://localhost:12345/tonda/foo/bar/doc.pdf ~/doc.pdf
  
Stažení souboru doc.pdf do lokálního adresáře:
$ ftrest get http://localhost:12345/tonda/foo/bar/doc.pdf
 
Odstranění souboru doc.pdf: 
$ ftrest del http://localhost:12345/tonda/foo/bar/doc.pdf
  
Odstranění adresáře bar:
$ ftrest rmd http://localhost:12345/tonda/foo/bar
  
  
Testování
  
Implementace bude testována na standardní instalaci distribuce CentOS7. Můžete použít image pro CentOS dostupný zde (Přihlašovací údaje: user/user4lab, root/root4lab). 
  
Dokumentace
  
Dokumentace by měla obsahovat váš popis implementace ve formě formátovaného dokumentu s použitím MD značkovacího jazyka a strukturou odpovidající Unix manuálovým stránkám. Tento soubor bude mít název readme.md.
Předpokládá se rozumně napsaný zdrojový kód v C/C++. Tedy vhodné formátování, komentáře apod. 
 
Rozšíření 
 
Možná rozšíření jsou (za extra bodové hodnocení, dle rozsahu a kvality implementace až dalších 10bodů):
autentizace uživatele heslem a použítí Authorization string v hlavičce
použití kódování pro přenos dat jiné než identity (je možné použít vhodnou standartně dostupnou kompresní knihovnu) 
rozšíření existujících operací (ls příkaz s uvedením filtru)
další operace (vzdálený přesun/kopírování souboru)
Implementovaná rozšíření uveďte v dokumentaci. 
 
Odkazy:
REST tutoriál (http://www.restapitutorial.com)
Kniha o použití REST (https://www.amazon.com/REST-Practice-Hypermedia-Systems-Architecture/dp/0596805829/)
Informace o protokolu HTTP/1.1 (https://tools.ietf.org/html/rfc7231)
MD značkovací jazyk (https://guides.github.com/features/mastering-markdown/)
Struktura manuálových stránek pro Linux (http://www.tldp.org/HOWTO/Man-Page/q3.html) 
