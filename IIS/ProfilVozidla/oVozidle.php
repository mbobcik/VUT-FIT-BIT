<?php

if (isset($_POST["smazatVozidloId"])) {
    $ret = smazatVozidloId($_POST["smazatVozidloId"]);
    $_POST = array();
    if($ret == ""){
        $_SESSION["vozidloId"] = NULL;
        header("Location: index.php");
        die();
    }
    else{
        header("Location: ?page=ProfilVozidla&loc=vozidlo&ret=" . $ret);         
    }
}

if (isset($_POST["upravitVozidloId"])) {
    $ret = upravVozidloId($_POST);
    $_POST = array();
    header("Location: ?page=ProfilVozidla&loc=vozidlo&ret=" . $ret); 
}

if (isset($_POST["smazatPrukazId"])) {
    $ret = smazatTechnickyPrukazId($_POST["smazatPrukazId"]);
    $_POST = array();
    header("Location: ?page=ProfilVozidla&loc=vozidlo&ret=" . $ret); 
}

if (isset($_POST["upravitPrukazId"])) {
    $ret = upravTechnickyPrukaz($_POST);
    $_POST = array();
    header("Location: ?page=ProfilVozidla&loc=vozidlo&ret=" . $ret); 
}

if (isset($_POST["novyPrukazVozidloId"])) {
    $ret = novyTechnickyPrukaz($_POST);
    $_POST = array();
    header("Location: ?page=ProfilVozidla&loc=vozidlo&ret=" . $ret); 
}

$sql = "SELECT VOZIDLO.ID, VOZIDLO.ZNACKA, VOZIDLO.SPZ, VOZIDLO.BARVA, VOZIDLO.MODEL, VOZIDLO.ROKVYROBY, VOZIDLO.SKUPINAID, VOZIDLO.UKRADENO, SKUPINA.OZNACENI, SKUPINA.NAZEV FROM VOZIDLO INNER JOIN SKUPINA ON SKUPINA.ID = VOZIDLO.SKUPINAID WHERE VOZIDLO.ID = " . $_SESSION["vozidloId"];
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $vozidlo = $result->fetch_assoc();
} else {
    die("Neexistujici vozidlo.");
}


$sql = "SELECT TECHNICKA.ID, TECHNICKA.EVIDENCNICISLO, TECHNICKA.PLATNOSTOD, TECHNICKA.PLATNOSTDO FROM `TECHNICKA` INNER JOIN VOZIDLOTECHNICKA ON VOZIDLOTECHNICKA.TECHNICKAID = TECHNICKA.ID WHERE VOZIDLOTECHNICKA.VOZIDLOID = " . $_SESSION["vozidloId"] . " ORDER BY TECHNICKA.ID DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $technicke = $result;
} else {
    $technicke = NULL;
}

echo $_GET["ret"];

?>
<div id="udajeProfilu">
    <?php
    if ($vozidlo["UKRADENO"] == 1) {
        echo "<h2 style=\"color: red;\">Ukradeno</h2>";
    }
    ?>
    <form id="formUdajeVozidla" method="post" action="?page=ProfilVozidla&loc=vozidlo">
        <table style="margin-left: 0; padding: 10px">
            <script>
                function editUdajeOVozidle() {
                    var inputs = document.getElementsByClassName("inputUdajeOVozidle");
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].disabled = false;
                    }

                    if (document.getElementById("inputUdajeVozidlaZrusit") !== null)
                        return;

                    var inputZrus = document.createElement("input");
                    inputZrus.setAttribute("id", "inputUdajeVozidlaZrusit");
                    inputZrus.setAttribute("type", "button");
                    inputZrus.setAttribute("value", "Zrušit");
                    inputZrus.setAttribute("onClick", "zrusUpravu()");

                    var form = document.getElementById("formUdajeVozidla");
                    insertAfter(inputZrus, document.getElementById("upravitVozidloButton"));

                    var uprav = document.getElementById("upravitVozidloButton");
                    uprav.setAttribute("onClick", "provedUpravu()");
                    uprav.setAttribute("value", "Uložit");
                }

                function zrusUpravu() {
                    var inputs = document.getElementsByClassName("inputUdajeOVozidle");
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].disabled = true;

                        if (inputs[i].name === "spz")
                            inputs[i].value = <?php echo "\"" . $vozidlo["SPZ"] . "\""; ?>;

                        if (inputs[i].name === "znacka")
                            inputs[i].value = <?php echo "\"" . $vozidlo["ZNACKA"] . "\""; ?>;

                        if (inputs[i].name === "model")
                            inputs[i].value = <?php echo "\"" . $vozidlo["MODEL"] . "\""; ?>;

                        if (inputs[i].name === "rokVyroby")
                            inputs[i].value = <?php echo "\"" . $vozidlo["ROKVYROBY"] . "\""; ?>;

                        if (inputs[i].name === "skupina"){
                            var options = document.querySelectorAll("#formUdajeVozidla table tr td select option");
                            for(var j = 0; j < options.length; j++){
                                if(options[j].value == <?php echo "\"" . $vozidlo["SKUPINAID"] . "\""; ?>)
                                    document.getElementById("selectSkupina").selectedIndex = j;                                
                            }
                        }

                        if (inputs[i].name === "barva")
                            inputs[i].value = <?php echo "\"" . $vozidlo["BARVA"] . "\""; ?>;
                            
                        if (inputs[i].name === "ukradeno")
                            inputs[i].checked = <?php  if($vozidlo["BARVA"] == 1) echo "true"; else echo "false"; ?>;
                    }

                    var input = document.getElementById("inputUdajeVozidlaZrusit");
                    input.parentNode.removeChild(input);

                    var uprav = document.getElementById("upravitVozidloButton");
                    uprav.setAttribute("onClick", "editUdajeOVozidle()");
                    uprav.setAttribute("value", "Upravit");
                }

                function provedUpravu() {
                    var r = confirm("Skutečně chcete upravit informace tohoto vozidla?");
                    if (r === true) {

                        var form = document.getElementById("formUdajeVozidla");

                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", "upravitVozidloId");
                        hiddenField.setAttribute("value", <?php echo $_SESSION["vozidloId"]; ?>);
                        form.appendChild(hiddenField);

                        form.submit();
                    }
                }

                function smazVozidlo() {
                    var r = confirm("Skutečně chcete nenávratně smazat toto vozidlo?");
                    if (r === true) {
                        var form = document.createElement("form");
                        form.setAttribute("method", "post");
                        form.setAttribute("action", "?page=ProfilVozidla&loc=vozidlo");

                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", "smazatVozidloId");
                        hiddenField.setAttribute("value", <?php echo $_SESSION["vozidloId"]; ?>);

                        form.appendChild(hiddenField);

                        document.body.appendChild(form);
                        form.submit();
                    }
                }

                function insertAfter(newNode, referenceNode) {
                    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
                }
            </script>

            <?php
                $sql = "SELECT * FROM SKUPINA";
                $result = $conn->query($sql);
                for ($i = 0; $skupinaInfo = $result->fetch_assoc(); $i++) {
                    $seznamSkupin[$i] = $skupinaInfo;
                }
            ?>

            <tr><td>SPZ:</td><td><input class="inputUdajeOVozidle" type="text" name="spz" value="<?php echo $vozidlo["SPZ"]; ?>" disabled></td></tr>
            <tr><td>Značka:</td><td><input class="inputUdajeOVozidle" type="text" name="znacka" value="<?php echo $vozidlo["ZNACKA"] ?>" disabled></td></tr>
            <tr><td>Model:</td><td><input class="inputUdajeOVozidle" type="text" name="model" value="<?php echo $vozidlo["MODEL"] ?>" disabled></td></tr>
            <tr><td>Rok výroby:</td><td><input class="inputUdajeOVozidle" type="number" name="rokVyroby" value="<?php echo $vozidlo["ROKVYROBY"] ?>" disabled></td></tr>
            <tr><td>Barva:</td><td><input class="inputUdajeOVozidle" type="text" name="barva" value="<?php echo $vozidlo["BARVA"] ?>" disabled></td></tr>
            <tr><td>Skupina:</td><td>
                    <select id="selectSkupina" class="inputUdajeOVozidle" disabled name="skupina">
                        <?php
                            for($i = 0; $seznamSkupin[$i] != NULL; $i++){
                                echo "<option value=\"" . $seznamSkupin[$i]["ID"] . "\"";
                                if($seznamSkupin[$i]["ID"] == $vozidlo["SKUPINAID"])
                                    echo "selected";
                                echo ">" . $seznamSkupin[$i]["OZNACENI"] . "</option>";                                
                            }
                        ?>
                    </select>
                </td></tr>
            <tr><td>Ukradeno:</td><td><input class="inputUdajeOVozidle" type="checkbox" name="ukradeno" value="<?php echo $vozidlo["UKRADENO"] ?>" disabled <?php if($vozidlo["UKRADENO"] == 1) echo "checked" ?>></td></tr>
            <tr><td></td><td><input id="upravitVozidloButton" type="button" value="Upravit" onclick="editUdajeOVozidle()"  <?php if ($role["UPRAVAZAZNAMU"] != 1) echo "disabled"; ?>><input id="smazVozidloButton" type="button" value="Smazat" onclick="smazVozidlo()"  <?php if ($role["SMAZANIZAZNAMU"] != 1) echo "disabled"; ?>></td></tr>

        </table>
    </form>
    <hr>
</div>

<div id="prukazExpand"> 
    <script>
        function clickExpand(id) {
            if (document.getElementById("technicka-" + id + "-body").style.display === 'block') {
                document.getElementById("technicka-" + id + "-body").style.display = 'none';
                document.getElementById("expand-" + id + "-arrow").textContent = "\u25BC";
            } else {
                document.getElementById("technicka-" + id + "-body").style.display = 'block';
                document.getElementById("expand-" + id + "-arrow").textContent = "\u25B2";
            }
        }
    </script>

    <!-- NOVA TECHNICKA -->
    <script>
        function novyPrukazShow() {
            var rows = document.querySelectorAll("#formNovaTechnicka table tr");
            for (var i = 0; i < rows.length - 1; i++) {
                rows[i].style.display = "table-row";
            }

            var novyBtn = document.getElementById("novaTechnickaBtn");
            novyBtn.setAttribute("value", "Vytvořit");
            novyBtn.setAttribute("onClick", "vytvoritTechnickou()");

            var inputZrus = document.createElement("input");
            inputZrus.setAttribute("id", "zrusNovyPrukazBtn");
            inputZrus.setAttribute("type", "button");
            inputZrus.setAttribute("value", "Zrušit");
            inputZrus.setAttribute("onClick", "zrusitNovyPrukaz()");

            var form = document.getElementById("formNovaTechnicka");
            insertAfter(inputZrus, novyBtn);

        }

        function vytvoritTechnickou() {
            var r = confirm("Skutečně chcete vytvořit nový technický průkaz tomuto vozidlu?");
            if (r === true) {

                var form = document.getElementById("formNovaTechnicka");

                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", "novyPrukazVozidloId");
                hiddenField.setAttribute("value", <?php echo $_SESSION["vozidloId"] ?> );
                form.appendChild(hiddenField);

                form.submit();
            }
        }

        function zrusitNovyPrukaz() {
            var zrusBtn = document.getElementById("zrusNovyPrukazBtn");
            zrusBtn.parentNode.removeChild(zrusBtn);

            var rows = document.querySelectorAll("#formNovaTechnicka table tr");
            for (var i = 0; i < rows.length - 1; i++) {
                rows[i].style.display = "none";
            }

            var novyBtn = document.getElementById("novaTechnickaBtn");
            novyBtn.setAttribute("value", "Nový technický průkaz");
            novyBtn.setAttribute("onClick", "novyPrukazShow()");

            var inputs = document.querySelectorAll("#formNovaTechnicka table tr td input");
            for (var i = 0; i < inputs.length - 1; i++) {
                inputs[i].value = "";
            }
        }

    </script>

    <form id="formNovaTechnicka" method="post" action="?page=ProfilVozidla&loc=vozidlo">
        <table>
            <tr style="display: none"><td>Evidenční číslo (10 číslic):</td><td><input type="number" name="evidencniCislo"></td></tr>
            <tr style="display: none"><td>Platnost od:</td><td><input type="date" name="platnostOd"></td></tr>
            <tr style="display: none"><td>Platnost do:</td><td><input type="date" name="platnostDo"></td></tr>

            <tr><td></td><td><input id="novaTechnickaBtn" type="button" value="Nový technický průkaz" onclick="novyPrukazShow()"  <?php if ($role["PRIDANIZAZNAMU"] != 1) echo "disabled"; ?>></td></tr>
        </table>
    </form>
    <?php
    if ($technicke == NULL) {
        echo "Nebyly nalezeny žádně technické průkazy.";
    } else {
        // vypsani jednotlivych prokazu
        for ($i = 0; $technickaSeznam[$i] = $technicke->fetch_assoc(); $i++) {
            
        }

        for ($i = 0; $technickaSeznam[$i] != NULL; $i++) {
            $technicka = $technickaSeznam[$i];

            // platnost prukazu k aktualnimu dni
            $today = new DateTime('');
            $od = new DateTime($technicka["PLATNOSTOD"]);
            $do = new DateTime($technicka["PLATNOSTDO"]);
            $platny = $today->format("Y-m-d") < $do->format("Y-m-d") && $today->format("Y-m-d") > $od->format("Y-m-d");
            ?>
            <script>
                function upravitPrukaz(id, idPrukazu) {

                    var skupiny = JSON.parse(<?php echo "'" . json_encode($prukazy) . "'" ?>);
                    console.debug(skupiny);
                    var options = document.querySelectorAll("#selectSkupinyPrukazuText-" + id + " select option");
                    for (var i = 0; i < options.length; i++) {
                        for (var j = 0; j < skupiny[id]["SKUPINY"].length; j++) {
                            console.debug(skupiny[id]["SKUPINY"][j]["SKUPINAID"]);
                            console.debug(options[i].value);
                            if (options[i].value === skupiny[id]["SKUPINY"][j]["SKUPINAID"])
                                options[i].selected = 'selected';
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
                    var r = confirm("Skutečně chcete upravit informace tohoto technického průkazu?");
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
                    var r = confirm("Skutečně chcete nenávratně smazat tento technický průkaz?");
                    if (r === true) {
                        var form = document.createElement("form");
                        form.setAttribute("method", "post");
                        form.setAttribute("action", "?page=ProfilVozidla&loc=vozidlo");

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
            echo "<div class=\"expandHead\" onclick=\"clickExpand('" . $i . "')\"><span id=\"expand-" . $i . "-arrow\">&#x25BC</span>Evidenční číslo průkazu: " . $technicka["EVIDENCNICISLO"];
            if ($platny)
                echo " - Platný<br>";
            else
                echo " - Neplatný<br>";
            echo "</div>";

            // telo
            ?>
            <div id="<?php echo "technicka-" . $i . "-body" ?>" class="expandBody">
                <form id="<?php echo "formUdajeOPrukazu-" . $i ?>" method="post" action="?page=ProfilVozidla&loc=vozidlo">
                    <table>
                        <tr><td>Platnost od:</td><td><input class="<?php echo "inputUdajeOPrukazu-" . $i ?>" type="date" name="platnostOd" value="<?php echo $technicka["PLATNOSTOD"]; ?>" disabled><span id="<?php echo "inputUdajeOPrukazuPuvodniPlatOd-" . $i ?>" style="display: none;"><?php echo $technicka["PLATNOSTOD"]; ?></span></td></tr>
                        <tr><td>Platnost do:</td><td><input class="<?php echo "inputUdajeOPrukazu-" . $i ?>" type="date" name="platnostDo" value="<?php echo $technicka["PLATNOSTDO"]; ?>" disabled><span id="<?php echo "inputUdajeOPrukazuPuvodniPlatDo-" . $i ?>" style="display: none;"><?php echo $technicka["PLATNOSTDO"]; ?></span></td></tr>
                        <tr><td></td><td><input id="<?php echo "upravPrukazButton-" . $i ?>" type="button" value="Upravit" onclick="upravitPrukaz(<?php echo $i ?>, <?php echo $technicka["ID"] ?>)"  <?php if ($role["UPRAVAZAZNAMU"] != 1) echo "disabled"; ?>><input id="<?php echo "smazPrukazButton-" . $i ?>" type="button" value="Smazat" onclick="smazatPrukaz(<?php echo $technicka["ID"] ?>)"  <?php if ($role["SMAZANIZAZNAMU"] != 1) echo "disabled"; ?>></td></tr>
                    </table>
                </form>
            </div>      
            <?php
        }

        // expanze prvniho
        if (count($technickaSeznam) > 0) {
            echo '<script type="text/javascript">',
            'clickExpand(\'0\');',
            '</script>';
        }
    }
    ?>

</div>

<?php
$conn->close();
