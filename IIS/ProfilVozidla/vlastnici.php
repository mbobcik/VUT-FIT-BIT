<?php
if (isset($_POST["prepsatVozidloId"])) {    
    $ret = prepisVozidloIdRidici($_POST);  
    $_POST = array();
    header("Location: ?page=ProfilVozidla&loc=vlastnici&ret=" . $ret);
}

$sql = "SELECT RIDIC.ID, RIDIC.PRIJMENI, RIDIC.JMENO, RIDIC.DATUMNAROZENI, RIDIC.RODNECISLO, RIDICVOZIDLO.DATUMPREPSANI FROM RIDIC INNER JOIN RIDICVOZIDLO ON RIDICVOZIDLO.RIDICID = RIDIC.ID WHERE RIDICVOZIDLO.VOZIDLOID = " . $_SESSION["vozidloId"];
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $vlastnici = $result;
} else {
    $vlastnici = NULL;
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
                    hiddenField.setAttribute("name", "prepsatVozidloId");
                    hiddenField.setAttribute("value", <?php echo $_SESSION["vozidloId"] ?>);
                    form.appendChild(hiddenField);

                    form.submit();
                }                
            }
    </script>
    
    <?php 
        if ($role["UPRAVAZAZNAMU"] != 1) $disabled = "disabled";
        else $disabled = "";
    ?>
    
    <form id="prepsatVozidlo" method="POST" action="?page=ProfilVozidla&loc=vlastnici">
        <table>
            <tr><td>Přepsat vozidlo:</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>Rodné číslo řidiče:</td><td><input type="number" name="rc"></td><td>Přepsat k datu:</td><td><input type="date" name="kDatu"></td><td><input type="button" value="Přepsat vozidlo" onclick="prepsatVozidlo()" <?php echo $disabled ?>></td></tr>
        </table>
    </form>
    
    
    <?php        
        if($vlastnici === NULL){
            echo "Nebylu nalezeni žádní vlastníci vozidla,.";
        }
        else{
            ?><table class="myTable">
                <tr class="tableHead"><td>Datum přepsání</td><td>Příjmení</td><td>Jméno</td><td>Datum narození</td><td>Rodné číslo</td><td></td></tr><?php
                while ($vlastnik = $vlastnici->fetch_assoc()) {
                    echo "<tr class=\"tableBody\"><td>" . $vlastnik["DATUMPREPSANI"] . "</td><td>" . $vlastnik["PRIJMENI"] . "</td><td>" . $vlastnik["JMENO"] . "</td><td>" . $vlastnik["DATUMNAROZENI"] . "</td><td>" . $vlastnik["RODNECISLO"] . "</td><td><a href=\"?page=ProfilRidice&loc=oRidici&ridic=" . $vlastnik["ID"] . "\">></a></td></tr>";
                }
            
            
            ?></table><?php
        }
        
    ?>
    
    
</div>


