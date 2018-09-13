Popis:
Cílem projektu je implementovat klienta ipk-client, který se připojí k serveru, přijme od něj zadání matematické úlohy, kterou má vyřešit a zašle zpět výsledek.

Příklad spuštění:
ipk-client hostname
hostname může být IPv4/IPv6 adresa, není třeba uvažovat doménové jméno.
$./ipk-client 2001:db8::1

Protokol:
Komunikace mezi klientem a serverem probíhá pomocí zpráv HELLO, SOLVE, RESULT a BYE.

Klient zahájí komunikaci navázáním TCP spojení se serverem na portu 55555. Po navázání spojení zašle klient serveru zprávu HELLO [id]\n, kde ID je md5 hash studentského přihlašovacího jména (login).

Server může odpovědět zprávou SOLVE [číslo] [operace] [číslo]\n. Povolené operace jsou +, -, *, /. Klient vypočte matematickou operaci a výsledek vrátí ve zprávě RESULT [výsledek]\n. Pokud nebude klient schopný matematickou operaci z nějakého důvodu vyřešit, zašle zprávu RESULT ERROR\n.

Server může pokračovat další zprávou SOLVE, případně zaslat zprávu BYE [secret]\n. Klient hodnotu secret vypíše na standardní výstup a ukončí spojení. Pokud je secret ve formátu UNKNOWN, znamená to, že server nebyl schopen rozpoznat zadaný login zaslaný ve zprávě HELLO.

Příklad komunikace:
-> HELLO 3c765ea78206437d3a13b4fdacedcb64\n
<- SOLVE 4 * 2\n
-> RESULT 8\n
<- SOLVE 9 / 3\n
-> RESULT 3\n
<- BYE 4e5645a0a762e124e332f98a5293c3c0\n

Klient musí striktně dodržovat formát protokolu. Pokud mu přijde zpráva, která nebude ve správném formátu, bude zprávu ignorovat.

Dokumentace:

Dokumentace bude obsahovat váš popis implementace ve formě manuálové stránky dostupné pomocí příkazu man. Název dokumentace ipk-client.1. Dodržte standardní rozložení manuálové stránky a syntaxi. Omezení programu uveďte do sekce BUGS. 

Předpokládá se rozumně napsaný zdrojový kód v C/C++. Tedy vhodné formátování, komentáře apod

Testování
  
Implementace bude testována na standardní instalaci distribuce CentOS7. Můžete použít image pro CentOS dostupný zde (Přihlašovací údaje: user/user4lab, root/root4lab). 
Odkazy:
