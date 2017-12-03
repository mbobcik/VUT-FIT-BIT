<?php

if($role["ZOBRAZENIHISTORIE"] != 1){
    die("Uživatel nemá právo nahlížet do historie.");    
}

$sql = "SELECT UZIVATEL.JMENO, HISTORIE.CINNOST, HISTORIE.DATUMACAS FROM HISTORIE INNER JOIN UZIVATEL ON UZIVATEL.ID = HISTORIE.UZIVATELID ORDER BY HISTORIE.ID DESC";
$result = $conn->query($sql);
for($i = 0; $h = $result->fetch_assoc(); $i++){
    $historie[$i] = $h; 
}

?>

<table>
    <tr class="tableHead"><td>Datum a čas</td><td>Činnost</td><td>Provedl</td></tr>
    <?php for($i = 0; $historie[$i] != NULL; $i++){
        echo "<tr class=\"tableBody\"><td>" . $historie[$i]["DATUMACAS"] . "</td><td>" . $historie[$i]["CINNOST"] . "</td><td>" . $historie[$i]["JMENO"] . "</td></tr>";
    } ?>
</table>
    


