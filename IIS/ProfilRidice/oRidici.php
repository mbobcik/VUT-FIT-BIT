<?php

if (isset($_POST["smazatRidiceId"])) {
    $ret = smazatRidiceId($_POST["smazatRidiceId"]);
    $_POST = array();
    
    if($ret == ""){
        $_SESSION["ridicId"] = NULL;
        header("Location: index.php");
        die();
    }
    else{
        header("Location: ?page=ProfilRidice&loc=ridic&ret=" . $ret);         
    }
}

if (isset($_POST["upravitRidiceId"])) {  
    $ret = upravJmenoAAdresuRidiceId($_POST);   
    $_POST = array();
    header("Location: ?page=ProfilRidice&loc=ridic&ret=" . $ret); 
}

if (isset($_POST["smazatPrukazId"])) {
    smazatRidicskyPrukazId($_POST["smazatPrukazId"]);
    $_POST = array();
    header("Location: ?page=ProfilRidice&loc=ridic"); 
}

if (isset($_POST["upravitPrukazId"])) {
    $ret = upravRidicskyPrukaz($_POST);
    $_POST = array();
    header("Location: ?page=ProfilRidice&loc=ridic&ret=" . $ret); 
}

if (isset($_POST["novyPrukazRidicId"])) {
    $ret = novyRidicskyPrukaz($_POST);
    $_POST = array();
    header("Location: ?page=ProfilRidice&loc=ridic&ret=" . $ret); 
}

$sql = "SELECT RIDIC.PRIJMENI, RIDIC.JMENO, RIDIC.RODNECISLO, RIDIC.DATUMNAROZENI, ADRESA.ULICE, ADRESA.MESTO, ADRESA.PSC FROM RIDIC INNER JOIN ADRESA ON ADRESA.ID = RIDIC.ADRESAID WHERE RIDIC.ID = " . $_SESSION["ridicId"];
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $ridic = $result->fetch_assoc();
} else {
    die("Neexistující řidič.");
}

$sql = "SELECT  PRUKAZ.ID, PRUKAZ.SERIOVECISLO, PRUKAZ.PLATNOSTOD, PRUKAZ.PLATNOSTDO, RIDICPRUKAZ.PRUKAZID FROM PRUKAZ INNER JOIN RIDICPRUKAZ on RIDICPRUKAZ.PRUKAZID=PRUKAZ.ID WHERE RIDICPRUKAZ.RIDICID = " . $_SESSION["ridicId"] . " ORDER BY PRUKAZ.ID DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $prukazBasicInfo = $result;
} else {
    $prukazBasicInfo = NULL;
}

echo $_GET["ret"];

?>
<div id="udajeProfilu">
    <form id="formUdajeRidice" method="post" action="?page=ProfilRidice&loc=ridic">
        <table style="margin-left: 0; padding: 10px">
            <script>
                function editUdajeORidici() {
                    var inputs = document.getElementsByClassName("inputUdajeORidici");
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].disabled = false;
                    }

                    if (document.getElementById("inputUdajeRidiceZrusit") !== null)
                        return;

                    var inputZrus = document.createElement("input");
                    inputZrus.setAttribute("id", "inputUdajeRidiceZrusit");
                    inputZrus.setAttribute("type", "button");
                    inputZrus.setAttribute("value", "Zrušit");
                    inputZrus.setAttribute("onClick", "zrusUpravu()");

                    var form = document.getElementById("formUdajeRidice");
                    insertAfter(inputZrus, document.getElementById("upravitRidiceButton"));

                    var uprav = document.getElementById("upravitRidiceButton");
                    uprav.setAttribute("onClick", "provedUpravu()");
                    uprav.setAttribute("value", "Uložit");
                }

                function zrusUpravu() {
                    var inputs = document.getElementsByClassName("inputUdajeORidici");
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].disabled = true;

                        console.debug(inputs[i].name);

                        if (inputs[i].name === "prijmeni")
                            inputs[i].value = <?php echo "\"" . $ridic["PRIJMENI"] . "\""; ?>;

                        if (inputs[i].name === "jmeno")
                            inputs[i].value = <?php echo "\"" . $ridic["JMENO"] . "\""; ?>;

                        if (inputs[i].name === "datumNarozeni")
                            inputs[i].value = <?php echo "\"" . $ridic["DATUMNAROZENI"] . "\""; ?>;
                        
                        if (inputs[i].name === "rodneCislo")
                            inputs[i].value = <?php echo "\"" . $ridic["RODNECISLO"] . "\""; ?>;

                        if (inputs[i].name === "ulice")
                            inputs[i].value = <?php echo "\"" . $ridic["ULICE"] . "\""; ?>;

                        if (inputs[i].name === "mesto")
                            inputs[i].value = <?php echo "\"" . $ridic["MESTO"] . "\""; ?>;

                        if (inputs[i].name === "psc")
                            inputs[i].value = <?php echo "\"" . $ridic["PSC"] . "\""; ?>;
                    }

                    var input = document.getElementById("inputUdajeRidiceZrusit");
                    input.parentNode.removeChild(input);

                    var uprav = document.getElementById("upravitRidiceButton");
                    uprav.setAttribute("onClick", "editUdajeORidici()");
                    uprav.setAttribute("value", "Upravit");
                }

                function provedUpravu() {
                    var r = confirm("Skutečně chcete upravit informace tohoto řidiče?");
                    if (r === true) {

                        var form = document.getElementById("formUdajeRidice");

                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", "upravitRidiceId");
                        hiddenField.setAttribute("value", <?php echo $_SESSION["ridicId"]; ?>);
                        form.appendChild(hiddenField);

                        form.submit();
                    }
                }

                function smazOsobu() {
                    var r = confirm("Skutečně chcete nenávratně smazat tohoto řidiče?");
                    if (r === true) {
                        var form = document.createElement("form");
                        form.setAttribute("method", "post");
                        form.setAttribute("action", "?page=ProfilRidice&loc=ridic");

                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", "smazatRidiceId");
                        hiddenField.setAttribute("value", <?php echo $_SESSION["ridicId"]; ?>);

                        form.appendChild(hiddenField);

                        document.body.appendChild(form);
                        form.submit();
                    }
                }

                function insertAfter(newNode, referenceNode) {
                    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
                }
            </script>

            <tr><td>Příjmení:</td><td><input class="inputUdajeORidici" type="text" name="prijmeni" value="<?php echo $ridic["PRIJMENI"]; ?>" disabled></td></tr>
            <tr><td>Jméno:</td><td><input class="inputUdajeORidici" type="text" name="jmeno" value="<?php echo $ridic["JMENO"] ?>" disabled></td></tr>
            <tr><td>Datum narození:</td><td><input class="inputUdajeORidici" type="date" name="datumNarozeni" value="<?php echo $ridic["DATUMNAROZENI"] ?>" disabled></td></tr>
            <tr><td>Rodné číslo (10 číslic):</td><td><input class="inputUdajeORidici" type="number" name="rodneCislo" value="<?php echo $ridic["RODNECISLO"] ?>" disabled></td></tr>
            <tr><td>Adresa - Ulice:</td><td><input class="inputUdajeORidici" type="text" name="ulice" value="<?php echo $ridic["ULICE"] ?>" disabled></td></tr>
            <tr><td>Adresa - Město:</td><td><input class="inputUdajeORidici" type="text" name="mesto" value="<?php echo $ridic["MESTO"] ?>" disabled></td></tr>
            <tr><td>Adresa - PS:</td><td><input class="inputUdajeORidici" type="text" name="psc" value="<?php echo $ridic["PSC"] ?>" disabled></td></tr>

            <tr><td></td><td><input id="upravitRidiceButton" type="button" value="Upravit" onclick="editUdajeORidici()"  <?php if ($role["UPRAVAZAZNAMU"] != 1) echo "disabled"; ?>><input id="smazRidiceButton" type="button" value="Smazat" onclick="smazOsobu()"  <?php if ($role["SMAZANIZAZNAMU"] != 1) echo "disabled"; ?>></td></tr>

        </table>
    </form>
    <hr>
</div>

<div id="prukazExpand"> 
    <script>
        function clickExpand(id) {
            if (document.getElementById("prukaz-" + id + "-body").style.display === 'block') {
                document.getElementById("prukaz-" + id + "-body").style.display = 'none';
                document.getElementById("expand-" + id + "-arrow").textContent = "\u25BC";
            } else {
                document.getElementById("prukaz-" + id + "-body").style.display = 'block';
                document.getElementById("expand-" + id + "-arrow").textContent = "\u25B2";
            }
        }
    </script>

    <?php

    function vypisSkupiny($s) {
        if ($s == NULL) {
            return "";
        }

        $ret = "";
        $ret .= $s[0]["OZNACENI"];
        for ($i = 1; $s[$i] != NULL; $i++) {
            $ret .= ", " . $s[$i]["OZNACENI"];
        }
        return $ret;
    }

    // Vytvoreni seznamu vsech zkupin
        $sql = "SELECT * FROM SKUPINA";
        $result = $conn->query($sql);
        for ($i = 0; $skupinaInfo = $result->fetch_assoc(); $i++) {
            $seznamSkupin[$i] = $skupinaInfo;
        }
        
        // nastaveni prokazu a skupin
        if($prukazBasicInfo != NULL){
            for ($i = 0; $prukaz = $prukazBasicInfo->fetch_assoc(); $i++) {
                $prukazy[$i] = $prukaz;
            }
        }
        for ($i = 0; $prukazy[$i] != NULL; $i++) {
            $skupiny = NULL;
            $sql = "SELECT * FROM SKUPINA INNER JOIN PRUKAZSKUPINY ON SKUPINA.ID = PRUKAZSKUPINY.SKUPINAID WHERE PRUKAZSKUPINY.PRUKAZID = " . $prukazy[$i]["ID"];
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                for ($j = 0; $skupina = $result->fetch_assoc(); $j++) {
                    $skupiny[$j] = $skupina;                    
                }
            }
            $prukazy[$i]["SKUPINY"] = $skupiny;
            $prukazy[$i]["SKUPINYTEXT"] = vypisSkupiny($prukazy[$i]["SKUPINY"]);
        }
        
        ?>
    
        <script>
            function novyPrukazShow(){
                var rows = document.querySelectorAll("#formNovyPrukaz table tr");
                for(var i = 0; i < rows.length - 1; i++){
                    rows[i].style.display = "table-row";
                }
                
                var novyBtn = document.getElementById("novyPrukazBtn");
                novyBtn.setAttribute("value", "Vytvořit");
                novyBtn.setAttribute("onClick", "vytvoritPrukaz()");
                
                var inputZrus = document.createElement("input");
                inputZrus.setAttribute("id", "zrusNovyPrukazBtn");
                inputZrus.setAttribute("type", "button");
                inputZrus.setAttribute("value", "Zrušit");
                inputZrus.setAttribute("onClick", "zrusitNovyPrukaz()");

                var form = document.getElementById("formNovyPrukaz");
                insertAfter(inputZrus, novyBtn);
                
            }
            
            function vytvoritPrukaz(){
                var r = confirm("Skutečně chcete vytvořit nový průkaz pro tohoto řidiče?");
                if (r === true) {

                    var form = document.getElementById("formNovyPrukaz");

                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", "novyPrukazRidicId");
                    hiddenField.setAttribute("value", <?php echo $_SESSION["ridicId"];?>);
                    form.appendChild(hiddenField);

                    form.submit();
                }                
            }
            
            function zrusitNovyPrukaz(){                
                var zrusBtn = document.getElementById("zrusNovyPrukazBtn");
                zrusBtn.parentNode.removeChild(zrusBtn);
                
                var rows = document.querySelectorAll("#formNovyPrukaz table tr");
                for(var i = 0; i < rows.length - 1; i++){
                    rows[i].style.display = "none";
                }
                
                var novyBtn = document.getElementById("novyPrukazBtn");
                novyBtn.setAttribute("value", "Nový průkaz");
                novyBtn.setAttribute("onClick", "novyPrukazShow()");
                
                var options = document.querySelectorAll("#selectNovyPrukazSkupiny option");
                for (var i = 0; i < options.length; i++) {
                    options[i].selected = false;
                }
                
                var inputs = document.querySelectorAll("#formNovyPrukaz table tr td input");
                for(var i = 0; i < inputs.length-1; i++){
                    inputs[i].value = "";
                }
            }
            
        </script>
    
        <form id="formNovyPrukaz" method="post" action="?page=ProfilRidice&loc=ridic">
            <table>
                <tr style="display: none"><td>Sériové číslo (10 číslic):</td><td><input type="number" name="serioveCislo"></td></tr>
                <tr style="display: none"><td>Platnost od:</td><td><input type="date" name="platnostOd"></td></tr>
                <tr style="display: none"><td>Platnost do:</td><td><input type="date" name="platnostDo"></td></tr>
                <tr style="display: none"><td>Skupiny:</td><td>
                        <select id="selectNovyPrukazSkupiny" name="skupiny[]" multiple="true">
                        <?php
                        for ($j = 0; $seznamSkupin[$j] != NULL; $j++) {
                            echo "<option value=\"" . $seznamSkupin[$j]["ID"] . "\">" . $seznamSkupin[$j]["OZNACENI"] . "</option>";
                        }
                        ?>
                        </select> *Pro výběr více skupin, podžte tlačítko ctrl/comand a klikněte levím tlačítkem myši.
                    </td></tr>
                <tr><td></td><td><input id="novyPrukazBtn" type="button" value="Nový průkaz" onclick="novyPrukazShow()"  <?php if ($role["PRIDANIZAZNAMU"] != 1) echo "disabled"; ?>></td></tr>
            </table>
        </form>
    <?php
      
    if ($prukazBasicInfo == NULL) {
        echo "Nebyly nalezeny žádně řidičské průkazy.";
    } else {
    // vypsani jednotlivych prokazu
    for ($i = 0; $prukazy[$i] != NULL; $i++) {
        $prukaz = $prukazy[$i];

        // platnost prukazu k aktualnimu dni
        $today = new DateTime('');
        $od = new DateTime($prukaz["PLATNOSTOD"]);
        $do = new DateTime($prukaz["PLATNOSTDO"]);
        $platny = $today->format("Y-m-d") < $do->format("Y-m-d") && $today->format("Y-m-d") > $od->format("Y-m-d");
        ?>
            <script>
                function upravitPrukaz(id, idPrukazu) {
                    var text = document.getElementById("UdajeOPrukazuSkupinyText-" + id);
                    text.style.display = "none";

                    var text = document.getElementById("selectSkupinyPrukazuText-" + id);
                    text.style.display = "block";

                    var skupiny = JSON.parse(<?php echo "'" . json_encode($prukazy) . "'"?>);
                    console.debug(skupiny);
                    var options = document.querySelectorAll("#selectSkupinyPrukazuText-" + id + " select option");
                    for (var i = 0; i < options.length; i++) {
                        if(skupiny[id]["SKUPINY"] != null){
                            for(var j = 0; j < skupiny[id]["SKUPINY"].length; j++){
                                console.debug(skupiny[id]["SKUPINY"][j]["SKUPINAID"]);
                                console.debug(options[i].value);
                                if(options[i].value === skupiny[id]["SKUPINY"][j]["SKUPINAID"])
                                    options[i].selected = 'selected';
                            }
                        }
                    }
                    

                    var inputs = document.getElementsByClassName("inputUdajeOPrukazu-" + id);
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].disabled = false;
                    }


                    var upravBtn = document.getElementById("upravPrukazButton-" + id);
                    upravBtn.setAttribute("onClick", "ulozUpravuPrukazu(" + id + ", " + idPrukazu + ")");
                    upravBtn.value = "Uložit";

                    var inputZrus = document.createElement("input");
                    inputZrus.setAttribute("id", "zrusUpravuPrukazButton-" + id);
                    inputZrus.setAttribute("type", "button");
                    inputZrus.setAttribute("value", "Zrušit");
                    inputZrus.setAttribute("onClick", "zrusUpravuPrukazu(" + id + ")");

                    var form = document.getElementById("formUdajeOPrukazu-" + id);
                    insertAfter(inputZrus, upravBtn);

                    

                }

                function zrusUpravuPrukazu(id) {
                    var text = document.getElementById("UdajeOPrukazuSkupinyText-" + id);
                    text.style.display = "block";

                    var text = document.getElementById("selectSkupinyPrukazuText-" + id);
                    text.style.display = "none";

                    var options = document.querySelectorAll("#selectSkupinyPrukazuText-" + id + " select option");

                    for (var i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }


                    var inputs = document.getElementsByClassName("inputUdajeOPrukazu-" + id);
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].disabled = true;

                        if (inputs[i].name === "platnostOd") {
                            inputs[i].value = document.getElementById("inputUdajeOPrukazuPuvodniPlatOd-" + id).textContent;
                        }
                        if (inputs[i].name === "platnostDo") {
                            inputs[i].value = document.getElementById("inputUdajeOPrukazuPuvodniPlatDo-" + id).textContent;
                        }

                    }

                    var upravBtn = document.getElementById("upravPrukazButton-" + id);
                    upravBtn.setAttribute("onClick", "upravitPrukaz(" + id + ")");
                    upravBtn.value = "Upravit";

                    var zrusBtn = document.getElementById("zrusUpravuPrukazButton-" + id);
                    zrusBtn.parentNode.removeChild(zrusBtn);
                }

                function ulozUpravuPrukazu(id, idPrukazu) {
                    var r = confirm("Skutečně chcete upravit informace tohoto průkazu?");
                    if (r === true) {

                        var form = document.getElementById("formUdajeOPrukazu-" + id);

                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", "upravitPrukazId");
                        hiddenField.setAttribute("value", idPrukazu);
                        form.appendChild(hiddenField);
                        
                        form.submit();
                    }
                }

                function smazatPrukaz(idPrukazu) {
                    var r = confirm("Skutečně chcete nenávratně smazat průkaz řidiče?");
                    if (r === true) {
                        var form = document.createElement("form");
                        form.setAttribute("method", "post");
                        form.setAttribute("action", "?page=ProfilRidice&loc=ridic");

                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", "smazatPrukazId");
                        hiddenField.setAttribute("value", idPrukazu);

                        form.appendChild(hiddenField);

                        document.body.appendChild(form);
                        form.submit();
                    }
                }

            </script> 
        <?php
        // hlavicka
        echo "<div class=\"expandHead\" onclick=\"clickExpand('" . $i . "')\"><span id=\"expand-" . $i . "-arrow\">&#x25BC</span>Řidičský průkaz číslo: " . $prukaz["SERIOVECISLO"];
        if ($platny)
            echo " - Platný<br>";
        else
            echo " - Neplatný<br>";
        echo "</div>";

        // telo
        ?>
            <div id="<?php echo "prukaz-" . $i . "-body" ?>" class="expandBody">
                <form id="<?php echo "formUdajeOPrukazu-" . $i ?>" method="post" action="?page=ProfilRidice&loc=ridic">
                    <table>
                        <tr><td>Skupina:</td><td>
                                <span id="<?php echo "UdajeOPrukazuSkupinyText-" . $i ?>"><?php echo $prukaz["SKUPINYTEXT"] ?></span>
                                <span id="<?php echo "selectSkupinyPrukazuText-" . $i ?>" style="display: none"><select name="skupiny[]" multiple="true">
        <?php
        for ($j = 0; $seznamSkupin[$j] != NULL; $j++) {
            echo "<option value=\"" . $seznamSkupin[$j]["ID"] . "\">" . $seznamSkupin[$j]["OZNACENI"] . "</option>";
        }
        ?>
                                    </select> *Pro výběr více skupin, podžte tlačítko ctrl/comand a klikněte levím tlačítkem myši.</span>
                            </td></tr>
                        <tr><td>Platnost od:</td><td><input class="<?php echo "inputUdajeOPrukazu-" . $i ?>" type="date" name="platnostOd" value="<?php echo $prukaz["PLATNOSTOD"]; ?>" disabled><span id="<?php echo "inputUdajeOPrukazuPuvodniPlatOd-" . $i ?>" style="display: none;"><?php echo $prukaz["PLATNOSTOD"]; ?></span></td></tr>
                        <tr><td>Platnost do:</td><td><input class="<?php echo "inputUdajeOPrukazu-" . $i ?>" type="date" name="platnostDo" value="<?php echo $prukaz["PLATNOSTDO"]; ?>" disabled><span id="<?php echo "inputUdajeOPrukazuPuvodniPlatDo-" . $i ?>" style="display: none;"><?php echo $prukaz["PLATNOSTDO"]; ?></span></td></tr>
                        <tr><td></td><td><input id="<?php echo "upravPrukazButton-" . $i ?>" type="button" value="Upravit" onclick="upravitPrukaz(<?php echo $i ?>, <?php echo $prukaz["ID"] ?>)"  <?php if ($role["UPRAVAZAZNAMU"] != 1) echo "disabled"; ?>><input id="<?php echo "smazPrukazButton-" . $i ?>" type="button" value="Smazat" onclick="smazatPrukaz(<?php echo $prukaz["ID"] ?>)"  <?php if ($role["SMAZANIZAZNAMU"] != 1) echo "disabled"; ?>></td></tr>
                    </table>
                </form>
            </div>      
        <?php
    }

    // expanze prvniho
    if (count($prukazy) > 0) {
        echo '<script type="text/javascript">',
        'clickExpand(\'0\');',
        '</script>';
    }
}
?>

</div>

<?php
$conn->close();
