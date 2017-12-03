<?php
// todo: pridatPrestupek - z ridice? bude se pouzivat?

function connectDB() {
    require './conf.php';

// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

function smazatRidiceId($id){
    
    $conn = connectDB(); 
    
    $sql = "SELECT * FROM RIDICVOZIDLOPRESTUPEK WHERE RIDICID = " . $id;
    $result = $conn->query($sql);
    if ($result->num_rows > 0){
        return "Řidič má evidován nějaký přestupek, není možné ho smazat.";
    }
    
    $sql = "SELECT ADRESAID FROM RIDIC WHERE ID = " . $id;
    $result = $conn->query($sql);
    if ($result->num_rows > 0){
        $adresaId = $result->fetch_assoc()["ADRESAID"];
        $sql = "DELETE FROM ADRESA WHERE ID = " . $adresaId;
        $result = $conn->query($sql);
    }
    
    $sql = "SELECT PRUKAZID FROM RIDICPRUKAZ WHERE RIDICID = " . $id;
    $result = $conn->query($sql);  
    if ($result->num_rows > 0){
        while($prukazid = $result->fetch_assoc()["PRUKAZID"]){
            smazatRidicskyPrukazId($prukazid);
        }
    }   
    
    $sql = "DELETE FROM RIDIC WHERE ID = " . $id;
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Smazani ridice id= " . $id . "', NOW())";
    $result = $conn->query($sql);
    
    return "";
}

function upravJmenoAAdresuRidiceId($udaje){
    
    if(strlen($udaje["rodneCislo"]) < 10){
        return "Rodné číslo musí obsahovat alespoň 10 číslic.";
    }
        
    $today = new DateTime('');
    $novedatum = new DateTime($udaje["datumNarozeni"]);
    
    if($today->format("Y-m-d") <= $novedatum->format("Y-m-d")){
        return "Nelze zadávat budoucí datum narození.";
    }
    
    
    $conn = connectDB();
    
    $sql = "SELECT ID FROM RIDIC WHERE RODNECISLO = " . $udaje["rodneCislo"] . " AND ID != " . $udaje["upravitRidiceId"];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return "Nelze upravit řidiče. Zadané rodné číslo má již přidělené jiný řidič.";
    }

    $sql = "UPDATE RIDIC SET PRIJMENI= '" . $udaje["prijmeni"] . "', JMENO= '" . $udaje["jmeno"] . "', DATUMNAROZENI='" . $udaje["datumNarozeni"] . "', RODNECISLO = " . $udaje["rodneCislo"] . " WHERE ID=" . $udaje["upravitRidiceId"];
    $result = $conn->query($sql);
    
    $sql = "UPDATE ADRESA SET ULICE= '" . $udaje["ulice"] . "', MESTO= '" . $udaje["mesto"] . "', PSC='" . $udaje["psc"] . "' WHERE ID IN (SELECT ADRESAID FROM RIDIC WHERE ID =" . $udaje["upravitRidiceId"] . ")";
    $result = $conn->query($sql);    

    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Uprava informaci ridice id= " . $udaje["upravitRidiceId"] . "', NOW())";
    $result = $conn->query($sql);
    
    return "Řidič byl upraven";
    
}

function smazatRidicskyPrukazId($id){
    $conn = connectDB();  
    
    $sql = "DELETE FROM PRUKAZ WHERE ID = " . $id;
    $result = $conn->query($sql);
    $sql = "DELETE FROM RIDICPRUKAZ WHERE PRUKAZID = " . $id;
    $result = $conn->query($sql);
    $sql = "DELETE FROM PRUKAZSKUPINY WHERE PRUKAZID = " . $id;
    $result = $conn->query($sql);

    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Smazani ridicskeho prukazu id= " . $id . "', NOW())";
    $result = $conn->query($sql);
    
    return "Řidičský průkaz byl smazán.";
}

function upravRidicskyPrukaz($udaje){
    
    $conn = connectDB();  
    
    $od = new DateTime($udaje["platnostOd"]);
    $do = new DateTime($udaje["platnostDo"]);

    if($od->format("Y-m-d") >= $do->format("Y-m-d")){
        return "Platnost průkazu od nesmí být časově před paltnost průkazu do.";
    }  
    
    
    $sql = "DELETE FROM PRUKAZSKUPINY WHERE PRUKAZID = " . $udaje["upravitPrukazId"];
    $conn->query($sql);  
    if($udaje["skupiny"] != NULL){
        for($i = 0; $udaje["skupiny"][$i] != NULL; $i++){
            $sql = "INSERT INTO PRUKAZSKUPINY (PRUKAZID, SKUPINAID) VALUES (" . $udaje["upravitPrukazId"] . "," .$udaje["skupiny"][$i] . ")";
            $result = $conn->query($sql);  
        }
    }
    

        
    $sql = "UPDATE PRUKAZ SET PLATNOSTOD = '" . $udaje["platnostOd"] . "', PLATNOSTDO = '" . $udaje["platnostDo"] . "' WHERE ID = " . $udaje["upravitPrukazId"];
    $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Uprava ridicskeho prukazu id= " . $udaje["upravitPrukazId"] . "', NOW())";
    $result = $conn->query($sql);
    
    return "Průkaz byl úspěšně upraven.";
    
    
}

function novyRidicskyPrukaz($udaje){
    
    if(strlen($udaje["serioveCislo"]) < 10){
        return "Sériové číslo musí obsahovat alespoň 10 číslic.";
    }
    
    $conn = connectDB();

    $od = new DateTime($udaje["platnostOd"]);
    $do = new DateTime($udaje["platnostDo"]);

    $sql = "SELECT ID FROM PRIKAZ WHERE SERIOVECISLO = " . $udaje["serioveCislo"];
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return "Nelze vytvořit nový řidičský průkaz. Zadané sériové číslo je již přiděleno jinému průkazu.";
    }
    
    if($od->format("Y-m-d") >= $do->format("Y-m-d")){
        return "Platnost průkazu Od nesmí být časově před paltnost průkazu Do.";
    }  

    $sql = "INSERT INTO PRUKAZ VALUES (0, " . $udaje["serioveCislo"] . ", '" . $udaje["platnostOd"] . "', '" . $udaje["platnostDo"] . "')";
    $result = $conn->query($sql);
    
    $sql = "SELECT ID FROM PRUKAZ WHERE SERIOVECISLO = " . $udaje["serioveCislo"];
    $result = $conn->query($sql);
    if ($result->num_rows > 0){
        $prukazId = $result->fetch_assoc()["ID"];
    }
    else{
        return "Nastala chyba při vytváření řidičského průkazu. Prosím kontaktujte administrátora.";
    }
    
    $sql = "INSERT INTO RIDICPRUKAZ VALUES (0, " . $udaje["novyPrukazRidicId"] . ", " . $prukazId . ")";
    $result = $conn->query($sql);
    
    $sql = "DELETE FROM PRUKAZSKUPINY WHERE PRUKAZID = " . $prukazId;
    $result = $conn->query($sql);
    if($udaje["skupiny"] != NULL){
        for($i = 0; $udaje["skupiny"][$i] != NULL; $i++){
            $sql = "INSERT INTO PRUKAZSKUPINY (PRUKAZID, SKUPINAID) VALUES (" . $prukazId . "," .$udaje["skupiny"][$i] . ")";
            $result = $conn->query($sql);  
            echo $sql;
        }
    }
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Novy ridicsky prukaz (id=" . $prukazId . ") ridice id = " . $udaje["novyPrukazRidicId"] . "', NOW())";
    $result = $conn->query($sql);
    
    return "Řidičský průkaz byl vytvořen.";
    
}

function prepisVozidlo($udaje){
    $conn = connectDB(); 
    
    if($udaje["kDatu"] == NULL){
        return "Neplatné datum. Přepis neproběhne.";
    }
    
    $sql = "SELECT ID FROM VOZIDLO WHERE SPZ='" . $udaje["spz"] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1){
        $vozidloId = $result->fetch_assoc()["ID"];
    }
    else{        
        return "Neplatá SPZ. Přepis neproběhl.";
    }
    
    $sql = "INSERT INTO RIDICVOZIDLO VALUES(0, " . $udaje["prepsatVozidloRidicId"] . ", " . $vozidloId . ", '" . $udaje["kDatu"] . "')";
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Prepis vozidla (id=" . $vozidloId . ") ridici id = " . $udaje["prepsatVozidloRidicId"] . "', NOW())";
    $result = $conn->query($sql);
    
    return "Přepis proběhl v pořádku.";
    
}

function prepisVozidloIdRidici($udaje){
    $conn = connectDB(); 
    
    if($udaje["kDatu"] == NULL){
        return "Neplatné datum. Přepis neproběhne.";
    }
    
    $sql = "SELECT ID FROM RIDIC WHERE RODNECISLO=" . $udaje["rc"];
    $result = $conn->query($sql);
    if ($result->num_rows == 1){
        $ridicId = $result->fetch_assoc()["ID"];
    }
    else{        
        return "Neplaté rodné číslo. Přepis neproběhl.";
    }
    
    $sql = "INSERT INTO RIDICVOZIDLO VALUES(0, " . $ridicId . ", " . $udaje["prepsatVozidloId"] . ", '" . $udaje["kDatu"] . "')";
    $result = $conn->query($sql);
    
    return "Přepis proběhl v pořádku.";
    
}

function pridatPrestupek($udaje){
    
    echo "pridat prestupek ridici " . $udaje["pridatPrestupekRidiciId"] . "</br>";
    echo $udaje["evc"] . "</br>";   
    
    $conn = connectDB(); 
    
    $sql = "SELECT ID FROM PRESTUPEK WHERE EVIDENCNICISLO='" . $udaje["evc"] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1){
        $prestupekId = $result->fetch_assoc()["ID"];
    }
    else{        
        return "Neplaté evidenční číslo. Přidání neproběhlo.";
    }
    
    $sql = "SELECT ID FROM VOZIDLO WHERE SPZ='" . $udaje["spz"] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1){
        $vozidloId = $result->fetch_assoc()["ID"];
    }
    else{        
        return "Neplatá SPZ. Přidání neproběhlo.";
    }
    
    $sql = "INSERT INTO RIDICVOZIDLOPRESTUPEK VALUES (0, " . $udaje["pridatPrestupekRidiciId"] . ", " . $vozidloId .", " . $prestupekId . " )";
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Pridani prestupku (id=" . $prestupekId . ") ridici id = " . $udaje["pridatPrestupekRidiciId"] . ", vozidlo id = " . $vozidloId . "', NOW())";
    $result = $conn->query($sql);
    
    return "Přestupek byl úspěšně přidán.";
}

function pridatVinika($udaje){
    
    $conn = connectDB(); 
    
    $sql = "SELECT ID FROM RIDIC WHERE RODNECISLO='" . $udaje["rc"] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1){
        $ridicId = $result->fetch_assoc()["ID"];
    }
    else{        
        return "Neplatné rodné číslo. Přidání neproběhlo.";
    }
    
    $sql = "SELECT ID FROM VOZIDLO WHERE SPZ='" . $udaje["spz"] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1){
        $vozidloId = $result->fetch_assoc()["ID"];
    }
    else{        
        return "Neplatá SPZ. Přidání neproběhlo." . $udaje["spz"];
    }

    $sql = "INSERT INTO RIDICVOZIDLOPRESTUPEK VALUES (0 , " . $ridicId . ", " . $vozidloId . ", " . $udaje["pridatVinikaPrestupekIdId"] . ")";
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Pridani prestupku (id=" . $udaje["pridatVinikaPrestupekIdId"] . ") ridici id = " . $ridicId . ", vozidlo id = " . $vozidloId . "', NOW())";
    $result = $conn->query($sql);
    
    return "Viník byl přidán.";
    
}

function smazatProvineniId($id, $prestupekId){
    $conn = connectDB(); 
    
    $sql = "SELECT ID FROM RIDICVOZIDLOPRESTUPEK WHERE PRESTUPEKID = " . $prestupekId . " AND ID != " . $id;
    $result = $conn->query($sql);
    if ($result->num_rows == 0){
        return "Provinění nebude smazáno. Smazáním by přestupek přišel o všechny viníky (Můete ovšem smazat celý přestupek.). ";
    }
    
    $sql = "DELETE FROM RIDICVOZIDLOPRESTUPEK WHERE ID=" . $id;
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Smazani provineni (prestupek id=" . $prestupekId . ") ridici id = " . $id . "', NOW())";
    $result = $conn->query($sql);
    
    return "Provinění bylo smazáno";
    
}

function smazatVozidloId($id){
    
    $conn = connectDB(); 
    
    $sql = "SELECT * FROM RIDICVOZIDLOPRESTUPEK WHERE VOZIDLOID = " . $id;
    $result = $conn->query($sql);
    if ($result->num_rows > 0){
        return "Vozidlo má evidován nějaký přestupek, není možné ho smazat.";
    }
    
    $sql = "SELECT * FROM VOZIDLOTECHNICKA WHERE VOZIDLOID = " . $id;
    $result = $conn->query($sql);  
    var_dump($result);
    if ($result->num_rows > 0){
        while($prukazid = $result->fetch_assoc()["TECHNICKAID"]){
            echo smazatTechnickyPrukazId($prukazid);
            echo $prukazid;
        }
    }   
    
    $sql = "DELETE FROM RIDICVOZIDLO WHERE VOZIDLOID = " . $id;
    $result = $conn->query($sql);
    
    $sql = "DELETE FROM VOZIDLO WHERE ID = " . $id;
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Smazani vozidla id = " . $id . "', NOW())";
    $result = $conn->query($sql);
    
    return "dd";
}

function upravVozidloId($udaje){

    if(isset($udaje["ukradeno"])){
        $ukradeno = 1;
    }
    else{
        $ukradeno = 0;
    }
    
    $conn = connectDB();  
    
    $sql = "SELECT ID FROM VOZIDLO WHERE SPZ = '" . $udaje["spz"] . "' AND ID != " . $udaje["upravitVozidloId"];
    echo $sql;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return "Nelze upravit vozidlo, SPZ má již přiřazeno jiné vozidlo.";
    }
    
    $sql = "UPDATE VOZIDLO SET SPZ='" . $udaje["spz"] . "', BARVA='" . $udaje["barva"] . "', ZNACKA='" . $udaje["znacka"] . "', MODEL='" . $udaje["model"] . "', ROKVYROBY=" . $udaje["rokVyroby"] . ", SKUPINAID=" . $udaje["skupina"] . ", UKRADENO = " . $ukradeno . " WHERE ID = " . $udaje["upravitVozidloId"];
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Uprava vozidla id = " . $id . "', NOW())";
    $result = $conn->query($sql);
    
    return "Údaje vozidla byly upraveny.";   
    
}

function novyTechnickyPrukaz($udaje){
    
    if(strlen($udaje["evidencniCislo"]) != 10){
        return "Evidenční číslo musí obsahovat 10 číslic.";
    }
    
    $conn = connectDB();

    $od = new DateTime($udaje["platnostOd"]);
    $do = new DateTime($udaje["platnostDo"]);

    $sql = "SELECT ID FROM TECHNICKA WHERE EVIDENCNICISLO = " . $udaje["evidencniCislo"];
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return "Nelze vytvořit nový technický průkaz. Zadané evidenční číslo je již přiděleno jinému průkazu.";
    }
    
    if($od->format("Y-m-d") >= $do->format("Y-m-d")){
        return "Platnost průkazu Od nesmí být časově před paltnost průkazu Do.";
    }  

    $sql = "INSERT INTO TECHNICKA VALUES (0, " . $udaje["evidencniCislo"] . ", '" . $udaje["platnostOd"] . "', '" . $udaje["platnostDo"] . "')";
    $result = $conn->query($sql);
    
    $sql = "SELECT ID FROM TECHNICKA WHERE EVIDENCNICISLO = " . $udaje["evidencniCislo"];
    $result = $conn->query($sql);
    if ($result->num_rows > 0){
        $prukazId = $result->fetch_assoc()["ID"];
    }
    else{
        return "Nastala chyba při vytváření technického průkazu. Prosím kontaktujte administrátora.";
    }
    
    $sql = "INSERT INTO VOZIDLOTECHNICKA VALUES (0, " . $udaje["novyPrukazVozidloId"] . ", " . $prukazId . ")";
    $result = $conn->query($sql);
        
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Novy technicky prukaz (id=" . $prukazId . ") vozidla id = " . $udaje["novyPrukazVozidloId"] . "', NOW())";
    $result = $conn->query($sql);
    
    return "Technický průkaz byl vytvořen.";
    
}

function smazatTechnickyPrukazId($id){
    $conn = connectDB();  
    
    $sql = "DELETE FROM TECHNICKA WHERE ID = " . $id;
    $result = $conn->query($sql);
    $sql = "DELETE FROM VOZIDLOTECHNICKA WHERE TECHNICKAID = " . $id;
    $result = $conn->query($sql);

    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Smazan technicky prukaz id = " . $id . "', NOW())";
    $result = $conn->query($sql);
    
    return "Technický průkaz byl smazán.";
}

function upravTechnickyPrukaz($udaje){
    
    echo "Uprav tech  prukaz id " . $udaje["upravitPrukazId"] . "</br>";

    echo $udaje["platnostOd"] . "</br>";
    echo $udaje["platnostDo"] . "</br>";
    
    $conn = connectDB();  
    
    $od = new DateTime($udaje["platnostOd"]);
    $do = new DateTime($udaje["platnostDo"]);

    if($od->format("Y-m-d") >= $do->format("Y-m-d")){
        return "Platnost průkazu od nesmí být časově před paltnost průkazu do.";
    } 
        
    $sql = "UPDATE TECHNICKA SET PLATNOSTOD = '" . $udaje["platnostOd"] . "', PLATNOSTDO = '" . $udaje["platnostDo"] . "' WHERE ID = " . $udaje["upravitPrukazId"];
    $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Upraven technicky prukaz id = " . $udaje["upravitPrukazId"] . "', NOW())";
    $result = $conn->query($sql);
    
    return "Průkaz byl úspěšně upraven.";
    
}

function smazatPrestupekId($id){    
    $conn = connectDB(); 
        
    $sql = "DELETE FROM RIDICVOZIDLOPRESTUPEK WHERE PRESTUPEKID = " . $id;
    $result = $conn->query($sql);
    
    $sql = "DELETE FROM PRESTUPEK WHERE ID = " . $id;
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Smazan technicky prukaz id = " . $id . "', NOW())";
    $result = $conn->query($sql);
    
    return "";
}

function upravPrestupekId($udaje){
    
    if(strlen($udaje["evc"]) < 10){
        return "Evidenční číslo musí obsahovat 10 číslic.";
    }
    
    $conn = connectDB();
    
    $d = DateTime::createFromFormat('Y-m-d H:i:s', $udaje["datumAcas"]);
    if(!($d && $d->format('Y-m-d H:i:s') == $udaje["datumAcas"])){
        return "Chybně zadané datum a čas.";
    }
    
    $sql = "SELECT ID FROM PRESTUPEK WHERE EVIDENCNICISLO = " . $udaje["evc"] . " AND ID != " . $udaje["upravitPrestupekId"];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return "Nelze upravit přestupek. Zadané evidenční číslo má již přidělené jiný přetupek.";
    }
    
    $sql = "UPDATE PRESTUPEK SET EVIDENCNICISLO= '" . $udaje["evc"] . "', DATUMACAS= '" . $udaje["datumAcas"] . "', MISTO='" . $udaje["misto"] . "', PRESTUPEKTYPID= " . $udaje["typ"] . " WHERE ID=" . $udaje["upravitPrestupekId"];
    $result = $conn->query($sql);

    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Upraven prestupek id = " . $udaje["upravitPrestupekId"] . "', NOW())";
    $result = $conn->query($sql);
    
    return "Přestupek byl upraven.";
    
}

function novyUzivatel($udaje){    
    
    $conn = connectDB();
        
    if($udaje["userName"] == NULL || $udaje["pass"] == NULL)
        return "Nebyly zadány správné údaje";
    
    if(strlen($udaje["pass"]) < 8)
        return "Nedostatečná délka hesla.";
    
    if($udaje["pass"] !== $udaje["passAgain"])
        return "Heslo a kontrolní heslo se neshodují.";
    
    $sql = "SELECT ID FROM UZIVATEL WHERE JMENO = " . $udaje["userName"];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return "Uživatel se stejným jménem již existuje.";
    }
    
    $sql = "INSERT INTO UZIVATEL VALUE (0, '" . $udaje["userName"] . "', '" . md5($udaje["pass"]) . "', " . $udaje["role"] . ")";
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Novy uzivatel userName = " . $udaje["userName"] . "', NOW())";
    $result = $conn->query($sql);
    
    return "Uživatel byl vytvořen.";
}

function login($udaje){
    $conn = connectDB();
    $sql = "SELECT * FROM UZIVATEL WHERE JMENO = '" . $udaje["userName"] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows != 1) {
        return "Uživatel zadal špatné uživatelské jméno.";
    }
    
    $uzivatel = $result->fetch_assoc();
            
    if(md5($udaje["pass"]) !== $uzivatel["HESLO"]){
        return "Špatné uživatelské heslo, nebo jméno.";
    }
            
    $_SESSION["uzivatelId"] = $uzivatel["ID"];
    
    return "";
    
}

function logout(){    
    session_unset();
}

function chPass($udaje){
    $conn = connectDB();

    if($udaje["newPass"] == NULL)
        return "Nebyly zadány správné údaje.";
    
    if(strlen($udaje["newPass"]) < 8)
        return "Nedostatečná délka hesla.";
    
    if($udaje["newPass"] !== $udaje["newPassA"])
        return "Heslo a kontrolní heslo se neshodují.";
    
    $sql = "SELECT * FROM UZIVATEL WHERE JMENO = '" . $udaje["userName"] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        return "Uživatel nebyl nalezen. Heslo nebude změněno.";
    }
    
    $id = $result->fetch_assoc()["ID"];
    
    $sql = "UPDATE UZIVATEL SET HESLO='" . md5($udaje["newPass"]) . "' WHERE ID=" . $id;
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Zmena hesla uzivatele userName = " . $udaje["userName"] . "', NOW())";
    $result = $conn->query($sql);
    
    return "Heslo bylo změněno.";
    
}

function smazatUzivatele($id){
    $conn = connectDB();
    
    if($id == $_SESSION["uzivatelId"])
        logout();
    
    $sql = "DELETE FROM UZIVATEL WHERE ID = " . $id;
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Smazani uzivatele id= " . $id . "', NOW())";
    $result = $conn->query($sql);
    
    return "Uživatel byl smazán.";
}

function upravRoli($id, $roleId){
    
    $conn = connectDB();
    
    if($id == $_SESSION["uzivatelId"])
        logout();
    
    $sql = "UPDATE UZIVATEL SET ROLEID = " . $roleId . " WHERE ID = " . $id;
    $result = $conn->query($sql);    
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Uprava role uzivatele id= " . $id . "', NOW())";
    $result = $conn->query($sql);
    
    return "Uživatel byl upraven";
}

function pridatPrestupekVozidla($udaje){
        
    $conn = connectDB(); 
    
    $sql = "SELECT ID FROM PRESTUPEK WHERE EVIDENCNICISLO='" . $udaje["evc"] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1){
        $prestupekId = $result->fetch_assoc()["ID"];
    }
    else{        
        return "Neplaté evidenční číslo. Přidání neproběhlo.";
    }
    
    $sql = "SELECT ID FROM RIDIC WHERE RODNECISLO=" . $udaje["rc"];
    $result = $conn->query($sql);
    if ($result->num_rows == 1){
        $ridicId = $result->fetch_assoc()["ID"];
    }
    else{        
        return "Neplaté rodné číslo. Přidání neproběhlo.";
    }
    
    $sql = "INSERT INTO RIDICVOZIDLOPRESTUPEK VALUES (0, " . $ridicId . ", " . $udaje["pridatPrestupekVozidluId"] .", " . $prestupekId . " )";
    $result = $conn->query($sql);
    
    $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Pridani prestupku (id=" . $prestupekId . ") ridici id = " . $ridicId . ", vozidlo id = " . $udaje["pridatPrestupekVozidluId"] . "', NOW())";
    $result = $conn->query($sql);
    
    return "Přestupek byl úspěšně přidán.";
}