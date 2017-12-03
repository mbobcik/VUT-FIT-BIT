<?php
if (isset($_POST["smazatPrestupekId"])) {
    $ret = smazatPrestupekId($_POST["smazatPrestupekId"]);
    $_POST = array();
    if($ret == ""){
        $_SESSION["prestupekId"] = NULL;
        header("Location: index.php");
        die();
    }
    else{
        header("Location: ?page=ProfilPrestupku&loc=prestupek&ret=" . $ret);         
    }
}

if (isset($_POST["upravitPrestupekId"])) {
    $ret = upravPrestupekId($_POST);
    $_POST = array();
    header("Location: ?page=ProfilPrestupku&loc=prestupek&ret=" . $ret); 
}

$sql = "SELECT PRESTUPEK.ID, PRESTUPEK.DATUMACAS, PRESTUPEK.MISTO, PRESTUPEK.EVIDENCNICISLO, PRESTUPEKTYP.ID AS PRESTUPEKTYPID, PRESTUPEKTYP.NAZEV, PRESTUPEKTYP.POPIS FROM PRESTUPEK INNER JOIN PRESTUPEKTYP ON PRESTUPEK.PRESTUPEKTYPID=PRESTUPEKTYP.ID WHERE PRESTUPEK.ID = " . $_SESSION["prestupekId"];
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $prestupek = $result->fetch_assoc();
} else {
    die("Neexistujici přestupek.");
}


echo $_GET["ret"];
?>
<div id="udajeProfilu">
<?php
if ($vozidlo["UKRADENO"] == 1) {
    echo "<h2 style=\"color: red;\">Ukradeno</h2>";
}
?>
    <form id="formUdajePrestupku" method="post" action="?page=ProfilPrestupku&loc=prestupek">
        <table style="margin-left: 0; padding: 10px">
            
            <?php
                $sql = "SELECT * FROM PRESTUPEKTYP";
                $result = $conn->query($sql);
                for ($i = 0; $prestupekInfo = $result->fetch_assoc(); $i++) {
                    $seznamTypuPrestupku[$i] = $prestupekInfo;
                }
            ?>
            
            <script>
                function editUdajeOPrestupku() {
                    var inputs = document.getElementsByClassName("inputUdajeOPrestupku");
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].disabled = false;
                    }

                    if (document.getElementById("inputUdajePrestupkuZrusit") !== null)
                        return;

                    var inputZrus = document.createElement("input");
                    inputZrus.setAttribute("id", "inputUdajePrestupkuZrusit");
                    inputZrus.setAttribute("type", "button");
                    inputZrus.setAttribute("value", "Zrušit");
                    inputZrus.setAttribute("onClick", "zrusUpravu()");

                    var form = document.getElementById("formUdajePrestupku");
                    insertAfter(inputZrus, document.getElementById("upravitPrestupekButton"));

                    var uprav = document.getElementById("upravitPrestupekButton");
                    uprav.setAttribute("onClick", "provedUpravu()");
                    uprav.setAttribute("value", "Uložit");
                }

                function zrusUpravu() {
                    var inputs = document.getElementsByClassName("inputUdajeOPrestupku");
                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].disabled = true;

                        if (inputs[i].name === "evc")
                            inputs[i].value = <?php echo "\"" . $prestupek["EVIDENCNICISLO"] . "\""; ?>;

                        if (inputs[i].name === "datumAcas")
                            inputs[i].value = <?php echo "\"" . $prestupek["DATUMACAS"] . "\""; ?>;

                        if (inputs[i].name === "misto"){
                            var j = JSON.parse(<?php echo "'" . str_replace("\\", "\\\\", json_encode($prestupek["MISTO"])) . "'" ?>);
                            inputs[i].value = j;
                            
                        }

                        if (inputs[i].name === "typ") {
                            var options = document.querySelectorAll("#formUdajePrestupku table tr td select option");
                            for (var j = 0; j < options.length; j++) {
                                if (options[j].value == <?php echo "\"" . $prestupek["PRESTUPEKTYPID"] . "\""; ?>){
                                    document.getElementById("selectTypPrestupku").selectedIndex = j;
                                    selectChange();
                                }
                            }
                        }
                    }
                    
                    var input = document.getElementById("inputUdajePrestupkuZrusit");
                    input.parentNode.removeChild(input);

                    var uprav = document.getElementById("upravitPrestupekButton");
                    uprav.setAttribute("onClick", "editUdajeOPrestupku()");
                    uprav.setAttribute("value", "Upravit");
                }

                function provedUpravu() {
                    var r = confirm("Skutečně chcete upravit informace tohoto přestupku?");
                    if (r === true) {

                        var form = document.getElementById("formUdajePrestupku");

                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", "upravitPrestupekId");
                        hiddenField.setAttribute("value", <?php echo $_SESSION["prestupekId"]; ?>);
                        form.appendChild(hiddenField);

                        form.submit();
                    }
                }

                function smazPrestupek() {
                    var r = confirm("Skutečně chcete nenávratně smazat tento přestupek?");
                    if (r === true) {
                        var form = document.createElement("form");
                        form.setAttribute("method", "post");
                        form.setAttribute("action", "?page=ProfilPrestupku&loc=prestupek");

                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", "smazatPrestupekId");
                        hiddenField.setAttribute("value", <?php echo $_SESSION["prestupekId"]; ?>);

                        form.appendChild(hiddenField);

                        document.body.appendChild(form);
                        form.submit();
                    }
                }

                function selectChange(){
                    var j = JSON.parse(<?php echo "'" . str_replace("\\", "\\\\", json_encode($seznamTypuPrestupku)) . "'" ?>);
                    var options = document.querySelectorAll("#selectTypPrestupku option");
                    var num = document.getElementById("selectTypPrestupku");
                    for(var i = 0; i < j.length; i++){                        
                        if(j[i]["ID"] == options[num.selectedIndex].value)
                            document.getElementById("prestupekPopis").innerHTML = j[i]["POPIS"];
                    }
                }

                function insertAfter(newNode, referenceNode) {
                    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
                }
            </script>
            <tr><td>Evidenční číslo (10 číslic):</td><td><input class="inputUdajeOPrestupku" type="number" name="evc" value="<?php echo $prestupek["EVIDENCNICISLO"]; ?>" disabled></td></tr>
            <tr><td>Datum a čas:</td><td><input class="inputUdajeOPrestupku" type="datetime" name="datumAcas" value="<?php echo $prestupek["DATUMACAS"] ?>" disabled></td></tr>
            <tr><td>Místo:</td><td><textarea class="inputUdajeOPrestupku" rows="5" name="misto" disabled><?php echo $prestupek["MISTO"] ?></textarea></td></tr>
            <tr><td>Typ přestupku:</td><td>
                    <select id="selectTypPrestupku" class="inputUdajeOPrestupku" disabled onchange="selectChange()"  name="typ">
                    <?php
                    for ($i = 0; $seznamTypuPrestupku[$i] != NULL; $i++) {
                        echo "<option value=\"" . $seznamTypuPrestupku[$i]["ID"] . "\"";
                        if ($seznamTypuPrestupku[$i]["ID"] == $prestupek["PRESTUPEKTYPID"])
                            echo "selected";
                        echo ">" . $seznamTypuPrestupku[$i]["NAZEV"] . "</option>";
                    }
                    ?>
                    </select>
                </td></tr>
            <tr><td>Popis:</td><td id="prestupekPopis"><?php echo $prestupek["POPIS"]; ?></td></tr>
            <tr><td></td><td><input id="upravitPrestupekButton" type="button" value="Upravit" onclick="editUdajeOPrestupku()"  <?php if ($role["UPRAVAZAZNAMU"] != 1) echo "disabled"; ?>><input id="smazPrestupekButton" type="button" value="Smazat" onclick="smazPrestupek()"  <?php if ($role["SMAZANIZAZNAMU"] != 1) echo "disabled"; ?>></td></tr>

        </table>
    </form>
</div>

<?php
$conn->close();
