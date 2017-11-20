<?php
/*
Vytvoril:  Martin Bobcik, xbobci00
1. projekt do IPP
CLS - analyza dedicnosti mezi tridamy v C++
*/

class Methods{
    public $name;   // ??
    public $privacy;
    public $returnType;
    public $isVirtual;
    public $isPureVirtual;
    public $isStatic;
    public $params;
    public $inheritedFrom;
}

class Attributes{
    public $name;   //??
    public $privacy;
    public $dataType;
    public $isVirtual;
    public $isStatic;
    public $inheritedFrom;
}

class ClObject{
    public $name; // ??
    public $inheritance;
    public $isAbstract;
    public $methodsArr;
    public $attributesArr;
}


$shortOpts = "h";
$shortOpts .= "i::";
$shortOpts .= "o::";
$shortOpts .= "p::";
$shortOpts .= "d::";
$longOpts = array(
    "help",
    "input::",
    "output::",
    "pretty-xml::",
    "details::"
);
$options = getopt($shortOpts, $longOpts);
//print_r($options);  //TODO smazat

if ((isset($options["h"]) and !$options["h"] xor isset($options["help"]) and !$options["help"])) {
    if (!(count($options) == 1)) {
        exit(1);
    }
    help();
}

$input;
$inputPath;
if(isset($options["i"]) ){ //pokud je zadan prepinac
    if(file_exists($options["i"])) { //pokud existuje file y jeho hodnoty
        $input = fopen($options["i"], 'r');
        $inputPath = $options["i"];
        if($input == false){ //selhani otevreni
            exit(2);
        }
    }else{
        exit(2);
    }
}elseif (isset($options["input"])){
    if(file_exists($options["input"])) {
        $input = fopen($options["input"], 'r');
        $inputPath = $options["input"];
        if($input == false){
            exit(2);
        }
    }else{
        exit(2);
    }
} else { // jinak standardni vstup
    $input = fopen('php://stdin', 'r');
    $inputPath = 'php://stdin';
}

//$output;
$outputPath;
if(isset($options["output"])){ // pokud je zadan prepinac
        //$output = fopen($options["output"], 'w');
        $outputPath = $options["output"];
        /*if($output == false){ // selhani otevreni
            exit(3);
        }*/
}elseif (isset($options["o"])) {
        //$output = fopen($options["o"], 'w');
        $outputPath = $options["o"];
        /*if($output == false){
            exit(3);
        }*/
}else {
    //$output = fopen('php://stdout', 'w'); //standardni vystup
    $outputPath = 'php://stdout';
}

$indent;
if(isset($options["pretty-xml"])){       //pokud je nastaven prepinac
    if($options["pretty-xml"] == false){ // pokud neni nastaven parametr
        $indent = 4;
    }else{                               //pokud je nastaven par.
        $indent = $options["pretty-xml"];
    }
}elseif (isset($options["p"])){
    if ($options["p"] == false){
        $indent = 4;
    }else{
        $indent = $options["p"];
    }
}else{
    $indent = 1;
}

$details;$detailsClass;
if (isset($options["details"])){
    if($options["details"] == false){
        $details = true;
        $detailsClass = false;
    }else{
        $details = true;
        $detailsClass = $options["details"];
    }
}elseif (isset($options["d"])) {
    if ($options["d"] == false) {
        $details = true;
        $detailsClass = false;
    } else {
        $details = true;
        $detailsClass = $options["d"];
    }
}else{
    $details = false;
    $detailsClass = false;
}

$content = fread($input,filesize($inputPath)); // nactu cely soubor
$content .="\n\n"; // pricist dva entery kvuli regexu
fclose($input); // zavru vstupni soubor, dal neni potreba

//var_dump($content); echo "\n\n"; // todo smazat

//kouzelne zarikavadla
// https://regex101.com/r/wDB74P/2
$reGetClass = '/class\s+(\w+)\s*:?\s*:?([\s\w,]*){(.*^)(?=class|$)/msU';
//											             ^positive lookahead, matchne, ale neskonzumuje
$reGetInheritance = '/\s*((?:private)|(?:public)|(?:protected))?\s*(\w+),?/';
$reDataType = 'bool|char|char16_t|char32_t|wchar_t|signed char|short int|int|long int|long long int|unsigned char|unsigned short int|unsigned int|unsigned long int|unsigned long long int|float|double|long double|void';
$reAttribute = '/(?:(static|virtual)?\s+|)((?:(?:'.$reDataType.'|\w+)\s*(?:\*|\&)+)|(?:'.$reDataType.'|\w+)\s+)\s*(\w+);/ms';
$reMethod = '/((?:static|virtual)?)\s+((?:'.$reDataType.'|\w+)(?:(?:\s*(?:\*|\&)+\s*)|\s+))(\w+)\(([^)]*)\)(?:;|(\s*=0\s*;))/ms';
$reParams = '/((?:'.$reDataType.'|\w+)\s*(?:\*|\&)+\s*|(?:'.$reDataType.'|\w+)\s+)(\w+)/';
$reClassBody = '/(public|protected|private|):?(.+?)(?=public|protected|private|};)/sm';

preg_match_all($reGetClass, $content, $matches); // nacist zvlast tridy
// $matches = pole poli
// 0. pole = pole Full Matches
// 1. pole = pole 1. group matches ...  names
// 2. pole = pole 2. group matches ...  inheritance
// 3. pole = pole 3. group matches ...  class body

//   rozdeli dedicnosti
for ($i = 0; $i < count($matches[2]); $i++){ // --- tohle mohlo nejspis byt uvnitr nasledujiciho
    if (strcmp($matches[2][$i], ' ') != 0) {    // foru, a zmensil by se pocet dimenzi pole
        preg_match_all($reGetInheritance, $matches[2][$i], $tmp);
        $matches[2][$i] = $tmp;
    }else{
        $matches[2][$i] = false;
    }
}

$classArr = array();
for($i = 0; $i < count($matches[1]); $i++){ // prochazi tridu po tride
    $tmpClass = new ClObject();
    $tmpClass->name = $matches[1][$i];

    $methodsArr = array();
    $attributesArr = array();

    if($matches[2][$i] == false){ // pokud nema rodice
        $tmpClass->inheritance = false;
    }else{
        $tmpInheritance = array();
        for($j = 0; $j < count($matches[2][$i][0]); $j++){ //projdu vsechny rodice tridy
            $tmpInheritance[$matches[2][$i][2][$j]] = $matches[2][$i][1][$j]; //<< brutus
                            // jmeno rodice               pristupnost
        }
        $tmpClass->inheritance = $tmpInheritance;
    }

    if($tmpClass->inheritance!= false) {
        foreach ($tmpClass->inheritance as $parentName => $privacy) {
            if (isset($classArr[$parentName]) == false) {
                exit(4);  // pokud dedi z jeste nedefinovane tridy
            }
            $methodsArr = concatInheritance($classArr[$parentName]->methodsArr,$methodsArr,$parentName,$privacy);

            $attributesArr = concatInheritance($classArr[$parentName]->attributesArr, $attributesArr,$parentName,$privacy);
        }
    }

    //rozsekat body podle public, private, protected
    $found = preg_match_all($reClassBody, $matches[3][$i], $tmpBody);
     //tmpBody = pole tela podle pristupnosti
     //[0] = Full Match
     //[1] = pole privacy modifikatoru
     //[1][1] prazdne, ale == private
     //
     //[2] = pole tel tridy rozdelene podle privacy
   // print_r($tmpBody); // todo smazat
    if($found>0) {
        //z kazde casti vybrat metody a attributy
        for ($j = 0; $j < count($tmpBody[0]); $j++) { //prochazi privacy blok po bloku

            $found = 0;
            if(isset($tmpBody[2][$i])) {
                $found = preg_match_all($reAttribute, $tmpBody[2][$i], $attributeMatches);
            }
            if ($found > 0) {
                for ($m = 0; $m < count($attributeMatches[0]); $m++) {  // prochazi atribut po attr.
                    $tmpAttribute = new Attributes();
                    $tmpAttribute->name = $attributeMatches[3][$m]; // jmeno attr.
                    $tmpAttribute->dataType = trim($attributeMatches[2][$m]); // datovy typ
                    if(strpos($reDataType,$tmpAttribute->dataType) === false){ //pokud dat typ neexistuje
                        exit(4);
                    }
                    $tmpAttribute->inheritedFrom = false; // neni z nikama zdeden

                    if (strpos($tmpBody[1][$i], "public") !== 0) { // pokud je attribut v public bloku
                        $tmpAttribute->privacy = "public";
                    } elseif (strpos($tmpBody[1][$i], "protected") !== 0) { // pokud je attr v protected bloku
                        $tmpAttribute->privacy = "protected";
                    }elseif ($j == 0 or strpos($tmpBody[1][$i], "private") !== 0) { // 0 je vzdy private, ikdyz mozna prazdny
                        $tmpAttribute->privacy = "private";
                    }

                    if (strcmp($attributeMatches[1][$m], "static") == 0) { // attribut je staticky
                        $tmpAttribute->isStatic = true;
                    } else {
                        $tmpAttribute->isStatic = false;
                    }
                    if (strcmp($attributeMatches[1][$m], "virtual") == 0) { // attribut je virtualni
                        $tmpAttribute->isVirtual = true;
                    } else {
                        $tmpAttribute->isVirtual = false;
                    }

                    if ($tmpAttribute->isStatic && $tmpAttribute->isVirtual) {
                        exit(4); // clen tridy nesmi byt staticky a virtualni zaroven
                    }

                    $attributesArr[$tmpAttribute->name] = $tmpAttribute; // ulozim atribut do asociativniho pole atributu
                    // jmena attr. jsou klice
                }
            }

            $found = 0;
            if(isset($tmpBody[2][$i])) {
                $found = preg_match_all($reMethod, $tmpBody[2][$i], $methodMatches);
            }

            if ($found > 0) {
                for ($m = 0; $m < count($methodMatches[0]); $m++) {// projde vsechny metody tridy
                    $tmpMethod = new Methods();
                    $tmpMethod->name = $methodMatches[3][$m];
                    $tmpMethod->returnType = trim($methodMatches[2][$m]);
                    if(strpos($reDataType,$tmpMethod->returnType) === false){ // pokud zadany dat typ neexistuje
                       exit(4);
                    }
                    $tmpClass->isAbstract = false;
                    $tmpMethod->inheritedFrom = false; // neni z nikama zdedena
                    if (strpos($tmpBody[1][$i], "public") !== 0) { // pokud je metoda v public bloku
                        $tmpMethod->privacy = "public";
                    } elseif (strpos($tmpBody[1][$i], "protected") !== 0) { // pokud je metoda v protected bloku
                        $tmpMethod->privacy = "protected";
                    }elseif ($j == 0 or strpos($tmpBody[1][$i], "private") !== 0) { // 0 je vzdy private, ikdyz mozna prazdny
                        $tmpMethod->privacy = "private";
                    }

                    if (strcmp($methodMatches[1][$m], "static") == 0) { // pokud je metoda virtualni
                        $tmpMethod->isStatic = true;
                    } else {
                        $tmpMethod->isStatic = false;
                    }
                    if (strcmp($methodMatches[1][$m], "virtual") == 0) { // pokud je metoda staticka
                        $tmpMethod->isVirtual = true;
                        if (strpos($methodMatches[5][$m], "=0") !== 0) { // pokud je ciste virtualni
                            $tmpMethod->isPureVirtual = true;
                            $tmpClass->isAbstract = true; //trida je abstraktni, ma-li aspon jednu pure virtual metodu
                        } else {
                            $tmpMethod->isPureVirtual = false;
                        }
                    } else {
                        $tmpMethod->isVirtual = false;
                    }

                    if ($tmpMethod->isStatic && $tmpMethod->isVirtual) {
                        exit(4); // clen tridy nesmi byt staticky a virtualni zaroven
                    }

                    preg_match_all($reParams, $methodMatches[4][$m], $paramMatches);

                    $tmpMethod->params = array();
                    for ($n = 0; $n < count($paramMatches[0]); $n++) { // projde vsechny parametry metody a ulozi je do asociativniho pole pole
                        $tmpMethod->params[$paramMatches[2][$n]] = $paramMatches[1][$n];
                        //             //klic = jmeno                datovy typ
                        if(strpos($reDataType, $paramMatches[1][$n]) === false){ // pokud je zadany datovy typ spatny(neni mezi zadanyma)
                           exit(4);
                        }
                    }
                    $methodsArr[$tmpMethod->name] = $tmpMethod; // ulozi prochazenou metodu do asociativniho pole metod
                    // kde klice jsou jmena metod
                }
            }
        }
    }
    //ulozit do struktury

    $tmpClass->attributesArr = arrayClone( $attributesArr);
    $tmpClass->methodsArr = arrayClone($methodsArr);

    foreach ($tmpClass->methodsArr as $method) {
        if($method->isPureVirtual){
            $tmpClass->isAbstract = true;
        }
    }

    //ulozit tridu do asociativniho pole  pole trid, se jmeny jako klici
    $classArr[$tmpClass->name] = $tmpClass;
}

//print_r($classArr); // todo smazat

// Print the entire match result
// print_r($matches); // todo smazat
$writer = new XMLWriter();
$opened =$writer->openURI($outputPath);
if(!$opened){
    exit(3);
}
$indentingString = "";
for($i = 0; $i<$indent;$i++){
    $indentingString.=" ";
}
$writer->setIndentString($indentingString);
$writer->startDocument('1.0','UTF-8');
$writer->setIndent($indent);

if($details == false ){ // zadani a), strom dedicnosti
    outputA($classArr, $writer);
}elseif($details == true && $detailsClass == false){ // zadani b), pro vsechny tridy
    $writer->startElement('model');
    foreach ($classArr as $class) {
        outputB($class,$writer);
    }
    $writer->endElement();//model
}else{ // zadani b), pro urcitou tridu .. otestovat existenci
    if(isset($classArr[$detailsClass])) {
        outputB($classArr[$detailsClass], $writer);
    }
}
$writer->endDocument();
$writer->flush();


//logika tisku stromu dedicnosti
function outputA($classes,$writer){
    $writer->startElement('model');
    foreach ($classes as $className => $class) {
        if ($class->inheritance == false) { // pokud trida nema rodice, muzeme ji vytisknout
            outputAClass($class, $classes, $writer);
        }
    }
    $writer->endElement();
}
//tisk tridy pro strom dedicnosti
function outputAClass($currentClass,$classArr, $writer){
    $writer->startElement('class');
    $writer->writeAttribute("name", $currentClass->name);
    if($currentClass->isAbstract){
        $writer->writeAttribute("kind", "abstract");
    }else {
        $writer->writeAttribute("kind", "concrete");
    }
        foreach ($classArr as $childClass) {
            if($childClass->inheritance != false){
                foreach ($childClass->inheritance as $parentName=>$privacy) {
                    if(strcmp($parentName,$currentClass->name) == 0){
                        outputAClass($childClass,$classArr,$writer);
                    }
                }
        }
    }

    $writer->endElement();
}
//logika tisku tridy pro detailni popis ze zadani B
function outputB($currentClass, $writer){
    $writer->startElement('class');
    $writer->writeAttribute("name", $currentClass->name);
    if($currentClass->isAbstract){
        $writer->writeAttribute("kind", "abstract");
    }else {
        $writer->writeAttribute("kind", "concrete");
    }

    if($currentClass->inheritance!= false){
    $writer->startElement('inheritance');
        foreach ($currentClass->inheritance as $parentName=>$privacy) {
            $writer->startElement('from');
            $writer->writeAttribute("name", $parentName);
            if($privacy!= null) {
                $writer->writeAttribute("privacy", $privacy);
            }else{
                $writer->writeAttribute("privacy", "private");
            }
            $writer->endElement();//from
        }
    $writer->endElement();//inheritance
    }

    //zjistim, zda ma metoda nejake privacy bloky, metody a atributy
    $hasPublic = false;
    $hasPrivate = false;
    $hasProtected = false;
    $hasMethod = false;
    $hasAttribute = false;
    if($currentClass->methodsArr != null) {
        $hasMethod = true;
        foreach ($currentClass->methodsArr as $method) {
            if (strcmp($method->privacy, "public") == 0) {
                $hasPublic = true;
            } elseif (strcmp($method->privacy, "private") == 0) {
                $hasPrivate = true;
            } elseif (strcmp($method->privacy, "protected") == 0) {
                $hasProtected = true;
            }
        }
    }
    if($currentClass->attributesArr!= null) {
        $hasAttribute = true;
        foreach ($currentClass->attributesArr as $attribute) {
            if (strcmp($attribute->privacy, "public") == 0) {
                $hasPublic = true;
            } elseif (strcmp($attribute->privacy, "private") == 0) {
                $hasPrivate = true;
            } elseif (strcmp($attribute->privacy, "protected") == 0) {
                $hasProtected = true;
            }
        }
    }

    if($hasPublic){
        $writer->startElement('public');

        if($hasAttribute){
            $writer->startElement('attributes');
            foreach ($currentClass->attributesArr as $attribute) {
                outputBAttribute($attribute,$writer);
            }
            $writer->endElement();//attributes
        }
        if($hasMethod){
            $writer->startElement('methods');
            foreach ($currentClass->methodsArr as $method) {
                outputBMethod($method,$writer);
            }
            $writer->endElement();//methods
        }
        $writer->endElement();//public
    }
    if($hasPrivate){
        $writer->startElement('private');

        if($hasAttribute){
            $writer->startElement('attributes');
            foreach ($currentClass->attributesArr as $attribute) {
                outputBAttribute($attribute,$writer);
            }
            $writer->endElement();//attributes
        }
        if($hasMethod){
            $writer->startElement('methods');
            foreach ($currentClass->methodsArr as $method) {
                outputBMethod($method,$writer);
            }
            $writer->endElement();//methods
        }
        $writer->endElement();//private
    }
    if($hasProtected){
        $writer->startElement('protected');

        if($hasAttribute){
            $writer->startElement('attributes');
            foreach ($currentClass->attributesArr as $attribute) {
                outputBAttribute($attribute,$writer);
            }
            $writer->endElement();//attributes
        }
        if($hasMethod){
            $writer->startElement('methods');
            foreach ($currentClass->methodsArr as $method) {
                outputBMethod($method,$writer);
            }
            $writer->endElement();//methods
        }
        $writer->endElement();//protected
    }
    $writer->endElement();//class
}
//tisk attributu pro detailni popis
function outputBAttribute($attribute,$writer){
    $writer->startElement('attribute');
    $writer->writeAttribute('name', $attribute->name);
    $writer->writeAttribute('type', $attribute->dataType);
    if($attribute->isStatic) {
        $writer->writeAttribute('scope', 'static');
    }else{
        $writer->writeAttribute('scope', 'instance');
    }
    if($attribute->inheritedFrom != null){
        $writer->startElement('from');
        $writer->writeAttribute('name', $attribute->inheritedFrom);
        $writer->endElement(); // from
    }
    if($attribute->isVirtual) {
        $writer->startElement('virtual');
        $writer->writeAttribute('pure', 'no');
        $writer->endElement(); // virtual
    }
    $writer->endElement(); // attribute
}
//tisk metody pro detailni popis tridy
function outputBMethod($method,$writer){
    $writer->startElement('attribute');
    $writer->writeAttribute('name', $method->name);
    $writer->writeAttribute('type', $method->returnType);
    if($method->isStatic) {
        $writer->writeAttribute('scope', 'static');
    }else{
        $writer->writeAttribute('scope', 'instance');
    }
    if($method->inheritedFrom != null){
        $writer->startElement('from');
        $writer->writeAttribute('name', $method->inheritedFrom);
        $writer->endElement();//from
    }
    if($method->isVirtual) {
        $writer->startElement('virtual');
        if($method->isPureVirtual){
            $writer->writeAttribute('pure', 'yes');
        }else{
            $writer->writeAttribute('pure', 'no');
        }
        $writer->endElement();//virtual
    }

    $writer->startElement('arguments');

    foreach ($method->params as $paramName => $dataType) {
        $writer->startElement('argument');
        $writer->writeAttribute('name', $paramName);
        $writer->writeAttribute('type', $dataType);
        $writer->endElement();//argument
    }
    $writer->endElement(); // arguments
    $writer->endElement();//method
}
//tisk napovedy
function help(){ //todo
    echo "Skript pro analýzu dědičnosti mezi třídami popsanými zjednodušenou syntaxí pro soubory
programovacího jazyka C++11.\n\n";
    echo"-h --help      \tZobrazí tuto nápovědu\n";
    echo"-i --input     \tNastaví vstupní soubor, pokud tento přepínač chybí, za vstup se považuje standardní vstup\n";
    echo"-o --output    \tNastaví výstupní soubor, pokud tento přepínač chybí, výstup je přesměrován na standardní výstup\n";
    echo"-p --pretty-xml\tNastaví odsazování v XML souboru. Pokud tento přepínač chybí, odsazuje se pomocí jedné mezery.\n";
    echo"               \tPokud je tento přepínač zadán, ale chybí argument, odsazuje se čtyřmi mezerami, jinak se odsazuje\n";
    echo"               \tJinak se odsazuje argument * mezera.\n";
    echo"-d --details   \tPři zadání tohoto přepínače se místo stromu dědičností mezi třídami vypisují podrobné údaje\n";
    echo"               \to třídě zadané v argumentu. Pokud argument chybí, vypisují se podrobnosti o všech třídách.\n";
    exit(0);
}
//naklonuje jedno pole do druheho
function arrayClone($srcArray){
    $newArray = array();
    foreach($srcArray as $key => $value) {
        if(is_array($value)) $newArray[$key] = arrayClone($value);
        else if(is_object($value)) $newArray[$key] = clone $value;
        else $newArray[$key] = $value;
    }
    return $newArray;
}
//nacte do struktury tridy zdedene cleny a priradi jim prislusnou privacy
function concatInheritance($srcArray,$dstArray, $from, $privacy){
    $newArray = array();
    foreach($dstArray as $key => $value) {
        if(is_array($value)) $newArray[$key] = arrayClone($value);
        else if(is_object($value)) $newArray[$key] = clone $value;
        else $newArray[$key] = $value;
    }

    if(strcmp($privacy,"public") == 0) {
        foreach ($srcArray as $key => $value) {
            if(strcmp($value->privacy,"private") != 0) {
                if (is_array($value)) $newArray[$key] = arrayClone($value);
                else if (is_object($value)) $newArray[$key] = clone $value;
                else {$newArray[$key] = $value;}
                $newArray[$key]->inheritedFrom = $from;
            }
        }
    }else if(strcmp($privacy,"protected") == 0){
        foreach ($srcArray as $key => $value) {
            if(strcmp($value->privacy,"private") != 0) {
                if (is_array($value)) $newArray[$key] = arrayClone($value);
                else if (is_object($value)) $newArray[$key] = clone $value;
                else {$newArray[$key] = $value;}
                $newArray[$key]->inheritedFrom = $from;
                if (strcmp($value->privacy, "public") == 0) {
                    $newArray[$key]->privacy = "protected";
                }
            }
        }
    }else{
        foreach ($srcArray as $key => $value) {
            if(strcmp($value->privacy,"private") != 0) {
                if (is_array($value)) $newArray[$key] = arrayClone($value);
                else if (is_object($value)) $newArray[$key] = clone $value;
                else {$newArray[$key] = $value;}
                $newArray[$key]->inheritedFrom = $from;
                $newArray[$key]->privacy = "private";
            }
        }
    }

    return $newArray;
}
     // Asi jsem to mohl trochu vic okomentovat
?>