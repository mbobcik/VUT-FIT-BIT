<?php
if (isset($_POST["pridatPrestupekVozidluId"])) {    
    $ret = pridatPrestupekVozidla($_POST);  
    $_POST = array();
    header("Location: ?page=ProfilVozidla&loc=prestupky&ret = " . $ret);
}
$sql = "SELECT PRESTUPEK.ID, PRESTUPEK.DATUMACAS, PRESTUPEK.EVIDENCNICISLO FROM PRESTUPEK INNER JOIN RIDICVOZIDLOPRESTUPEK ON RIDICVOZIDLOPRESTUPEK.PRESTUPEKID = PRESTUPEK.ID INNER JOIN VOZIDLO ON VOZIDLO.ID = RIDICVOZIDLOPRESTUPEK.VOZIDLOID WHERE VOZIDLO.ID = " . $_SESSION["vozidloId"];
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $prestupky = $result;
} else {
    $prestupky = NULL;
}

echo $_GET["ret"];

?>

<div>
    
    <script>
        function pridatPrestupek(){
                var r = confirm("Skutečně chcete tomuto řidiči přidat přestupek?");
                if (r === true) {

                    var form = document.getElementById("pridatPrestupek");

                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", "pridatPrestupekVozidluId");
                    hiddenField.setAttribute("value", <?php echo $_SESSION["vozidloId"] ?>);
                    form.appendChild(hiddenField);

                    form.submit();
                }                
            }
    </script>
    
    <form id="pridatPrestupek" method="POST" action="?page=ProfilRidice&loc=prestupky">
        <table>
            <tr><td>Přidat přestupek:</td><td></td><td></td></tr>
            <tr><td>Evidenční číslo:</td><td><input type="text" name="evc"></td><td>Řidič (RC):</td><td><input type="text" name="rc"></td><td><input type="button" value="Přidat přestupek" onclick="pridatPrestupek()"></td></tr>
        </table>
    </form>
    
    <?php 
        if($prestupky === NULL){
            echo "Nebyla nalezana žádná vlastněná vozidla.";
        }
        else{
            ?><table class="myTable">
                <tr class="tableHead"><td>Datum</td><td>Evidenční číslo</td><td></td></tr><?php
                while ($prestupek = $prestupky->fetch_assoc()) {
                    echo "<tr class=\"tableBody\"><td>" . $prestupek["DATUMACAS"] ."</td><td>" . $prestupek["EVIDENCNICISLO"] ."</td><td><a href=\"?page=ProfilRidice&loc=prestupky&prestupky=" . $prestupek["ID"] . "\">></a></td></tr>";
                }
            
            
            ?></table><?php
        }
        
    ?>
    
    
</div>


