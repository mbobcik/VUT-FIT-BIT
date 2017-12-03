<?php
if (isset($_POST["pridatPrestupekRidiciId"])) {    
    $ret = pridatPrestupek($_POST);  
    $_POST = array();
    header("Location: ?page=ProfilRidice&loc=prestupky&ret = " . $ret);
}

$sql = "SELECT PRESTUPEK.ID, PRESTUPEK.DATUMACAS, PRESTUPEK.EVIDENCNICISLO FROM PRESTUPEK INNER JOIN RIDICVOZIDLOPRESTUPEK ON RIDICVOZIDLOPRESTUPEK.PRESTUPEKID = PRESTUPEK.ID INNER JOIN RIDIC ON RIDIC.ID = RIDICVOZIDLOPRESTUPEK.RIDICID WHERE RIDIC.ID = " . $_SESSION["ridicId"];
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
                    hiddenField.setAttribute("name", "pridatPrestupekRidiciId");
                    hiddenField.setAttribute("value", <?php echo $_SESSION["ridicId"] ?>);
                    form.appendChild(hiddenField);

                    form.submit();
                }                
            }
    </script>
    
    <form id="pridatPrestupek" method="POST" action="?page=ProfilRidice&loc=prestupky">
        <table>
            <tr><td>Přidat přestupek:</td><td></td><td></td></tr>
            <tr><td>Evidenční číslo:</td><td><input type="text" name="evc"></td><td>S vozidlem (SPZ):</td><td><input type="text" name="spz"></td><td><input type="button" value="Přidat přestupek" onclick="pridatPrestupek()"></td></tr>
        </table>
    </form>
    
    <?php 
        if($prestupky === NULL){
            echo "Nebyly nalezeny žádné přestupky.";
        }
        else{
            ?><table class="myTable">
                <tr class="tableHead"><td>Datum</td><td>Evidenční číslo</td><td></td></tr><?php
                while ($prestupek = $prestupky->fetch_assoc()) {
                    echo "<tr class=\"tableBody\"><td>" . $prestupek["DATUMACAS"] ."</td><td>" . $prestupek["EVIDENCNICISLO"] ."</td><td><a href=\"?page=ProfilPrestupku&loc=prestupek&prestupek=" . $prestupek["ID"] . "\">></a></td></tr>";
                }
            
            
            ?></table><?php
        }
        
    ?>
    
    
</div>


