<?php
if (isset($_POST["prepsatVozidloRidicId"])) {    
    $ret = prepisVozidlo($_POST);  
    $_POST = array();
    header("Location: ?page=ProfilRidice&loc=vozidla&ret=" . $ret);
}

$sql = "SELECT VOZIDLO.ID, VOZIDLO.SPZ, VOZIDLO.BARVA, VOZIDLO.ZNACKA, VOZIDLO.MODEL, VOZIDLO.ROKVYROBY FROM VOZIDLO INNER JOIN RIDICVOZIDLO ON RIDICVOZIDLO.VOZIDLOID = VOZIDLO.ID INNER JOIN RIDIC ON RIDIC.ID = RIDICVOZIDLO.RIDICID WHERE RIDIC.ID = " . $_SESSION["ridicId"] . " AND VOZIDLO.ID IN (SELECT VOZIDLOID FROM (SELECT * FROM(SELECT * FROM RIDICVOZIDLO ORDER BY DATUMPREPSANI DESC) X GROUP BY VOZIDLOID) Y WHERE RIDICID=" . $_SESSION["ridicId"] . ")";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $vozidla = $result;
} else {
    $vozidla = NULL;
}

echo $_GET["ret"];

?>

<div>
    
    <script>
        function prepsatVozidlo(){
                var r = confirm("Skutečně chcete přepsat vozidlo na tohoto řidiče?");
                if (r === true) {

                    var form = document.getElementById("prepsatVozidlo");

                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", "prepsatVozidloRidicId");
                    hiddenField.setAttribute("value", <?php echo $_SESSION["ridicId"] ?>);
                    form.appendChild(hiddenField);

                    form.submit();
                }                
            }
    </script>
    
    <?php 
        if ($role["UPRAVAZAZNAMU"] != 1) $disabled = "disabled";
        else $disabled = "";
    ?>
    
    <form id="prepsatVozidlo" method="POST" action="?page=ProfilRidice&loc=vozidla">
        <table>
            <tr><td>Přepsat vozidlo:</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>SPZ:</td><td><input type="text" name="spz"></td><td>Přepsat k datu:</td><td><input type="date" name="kDatu"></td><td><input type="button" value="Přepsat vozidlo" onclick="prepsatVozidlo()" <?php echo $disabled ?>></td></tr>
        </table>
    </form>
    
    <?php 
        if($vozidla === NULL){
            echo "Nebyla nalezana žádná vlastněná vozidla.";
        }
        else{
            ?><table class="myTable">
                <tr class="tableHead"><td>SPZ</td><td>Značka výrobce</td><td>Model</td><td>Rok výroby</td><td>Barva</td><td></td></tr><?php
                while ($vozidlo = $vozidla->fetch_assoc()) {
                    echo "<tr class=\"tableBody\"><td>" . $vozidlo["SPZ"] ."</td><td>" . $vozidlo["ZNACKA"] ."</td><td>" . $vozidlo["MODEL"] ."</td><td>" . $vozidlo["ROKVYROBY"] ."</td><td>" . $vozidlo["BARVA"] ."</td><td><a href=\"?page=ProfilVozidla&loc=vozidla&vozidlo=" . $vozidlo["ID"] . "\">></a></td></tr>";
                }
            
            
            ?></table><?php
        }
        
    ?>
    
    
</div>


