<?php
if (isset($_POST["pridatVinikaPrestupekIdId"])) {    
    $ret = pridatVinika($_POST);  
    $_POST = array();
    header("Location: ?page=ProfilPrestupku&loc=vinici&ret=" . $ret);
}

if (isset($_POST["smazatProvineniId"])) {    
    $ret = smazatProvineniId($_POST["smazatProvineniId"], $_SESSION["prestupekId"]);  
    $_POST = array();
    header("Location: ?page=ProfilPrestupku&loc=vinici&ret=" . $ret);
}

$sql = "SELECT RIDIC.PRIJMENI, RIDIC.JMENO, RIDIC.DATUMNAROZENI, RIDIC.RODNECISLO, RIDIC.ID AS RIDICID, VOZIDLO.SPZ, VOZIDLO.ID AS VOZIDLOID, RIDICVOZIDLOPRESTUPEK.ID AS PROVINENIID FROM RIDIC INNER JOIN RIDICVOZIDLOPRESTUPEK ON RIDICVOZIDLOPRESTUPEK.RIDICID=RIDIC.ID INNER JOIN VOZIDLO ON VOZIDLO.ID=RIDICVOZIDLOPRESTUPEK.VOZIDLOID WHERE RIDICVOZIDLOPRESTUPEK.PRESTUPEKID = " . $_SESSION["prestupekId"];
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $vinici = $result;
} else {
    $vinici = NULL;
}

echo $_GET["ret"];
?>

<div>
    
    <script>
        function pridatVinika(){
                var r = confirm("Skutečně chcete tomuto řidiči přidat přestupek?");
                if (r === true) {

                    var form = document.getElementById("pridatVinika");

                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", "pridatVinikaPrestupekIdId");
                    hiddenField.setAttribute("value", <?php echo $_SESSION["prestupekId"] ?>);
                    form.appendChild(hiddenField);

                    form.submit();
                }                
            }
            
        function smazatProvineniId(id){
            var r = confirm("Skutečně chcete tomuto řidiči odebrat přestupek?");
            if (r === true) {

                var form = document.createElement("form");
                form.setAttribute("method", "post");
                form.setAttribute("action", "?page=ProfilPrestupku&loc=vinici");

                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", "smazatProvineniId");
                hiddenField.setAttribute("value", id);

                form.appendChild(hiddenField);

                document.body.appendChild(form);
                form.submit();

            }                
        }
    </script>
    
    <?php 
        if ($role["UPRAVAZAZNAMU"] != 1) $disabled = "disabled";
        else $disabled = "";
    ?>
    
    <form id="pridatVinika" method="POST" action="?page=ProfilPrestupku&loc=vinici">
        <table>
            <tr><td>Přidat viníka:</td><td></td><td></td></tr>
            <tr><td>Rodné číslo viníka:</td><td><input type="number" name="rc"></td><td>Vozidlo (SPZ):</td><td><input type="text" name="spz"></td><td><input type="button" value="Přidat viníka" onclick="pridatVinika()" <?php echo $disabled ?>></td></tr>
        </table>
    </form>
    
    
    
    <?php     
        if($vinici === NULL){
            echo "Nebylu nalezeni žádní viníci.";
        }
        else{
            ?><table class="myTable">
                <tr class="tableHead"><td>Příjmení</td><td>Jméno</td><td>Datum Narození</td><td>Rodné číslo</td><td></td><td>SPZ</td><td></td><td></td></tr><?php
                while ($vinik = $vinici->fetch_assoc()) {
                    echo "<tr class=\"tableBody\"><td>" . $vinik["PRIJMENI"] . "</td><td>" . $vinik["JMENO"] . "</td><td>" . $vinik["DATUMNAROZENI"] . "</td><td>" . $vinik["RODNECISLO"] . "</td><td><a href=\"?page=ProfilRidice&loc=ridic&ridic=" . $vinik["RIDICID"] . "\">></a></td><td>" . $vinik["SPZ"] . "</td><td><a href=\"?page=ProfilVozidla&loc=vozidlo&vozidlo=" . $vinik["VOZIDLOID"] . "\">></a></td><td><input type=\"button\" value=\"Smazat provinění\" ". $disabled . " onClick=\"smazatProvineniId(" . $vinik["PROVINENIID"] . ")\"></td></tr>";
                }
            
            
            ?></table><?php
        }
        
    ?>
    
    
</div>


