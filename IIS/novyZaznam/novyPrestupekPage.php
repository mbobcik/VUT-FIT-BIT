<?php if ($role["PRIDANIZAZNAMU"] != 1) die("Nemáte dostatečná práva"); ?>

<div id="udajeProfilu">
    <form  method="post" action="?page=Vytvorit&loc=prestupek"  id="searchform"> 
      <table style="margin-left: 0; padding: 10px">
        <tr><td>Datum přestupku:</td><td><input  type="date" name="date"></td></tr>
        <tr><td>Místo:</td><td> <textarea rows="5" cols="25" name="place" ></textarea></td></tr>
        <tr><td>Evidenční číslo (10 číslic):</td><td> <input  type="int" name="ENumber"> </td></tr>
         <tr><td>Rodné číslo řidiče (10 číslic):</td><td><input  type="int" name="BNumber"> </td></tr>
         <tr><td>SPZ Vozidla:</td><td> <input  type="text" name="SPZ"></td></tr>
        <tr><td>Typ:</td><td>                          
          <select name="type">
            <?php 

            $sql = "SELECT ID,NAZEV FROM PRESTUPEKTYP";
            $skupiny = $conn->query($sql);
              while ($row = $skupiny->fetch_assoc()) {
                  echo "<option value=\"".$row["ID"]."\">".$row["NAZEV"]."</option>";
              }
             ?>
       </td></tr>


        <tr><td></td><td><input  type="submit" name="submit" value="Vytvořit" > </td></tr>
    </table>
</form> 

<?php
require_once 'functions.php';
if(isset($_POST['submit'])){
    $date   =$_POST['date']; 
    $place  =$_POST['place']; 
    $ENumber=$_POST['ENumber']; 
    $BNumber=$_POST['BNumber'];
    $SPZ    =  $_POST['SPZ'];
    $type   =  $_POST['type'];

    //cho "$date   ";
    //cho "$place  ";
    //cho "$ENumber";
    //cho "$BNumber";
    //cho "$SPZ    ";
    //cho "$type   ";


    if ($date == "" || $place == "" || $ENumber == 0 || $BNumber == 0 || $SPZ == "" || $type == 0) {
        die("<p>Vyplňte všechna povinná pole!</p><br>");
    }else {
        //check SPZ and get ID
        $VehicleID=-1;
        $sql = "SELECT ID FROM VOZIDLO WHERE SPZ = '$SPZ'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $VehicleID = $result->fetch_assoc()["ID"];
        }else{
            die("<p>Auto s touto SPZ v systému neexistuje!</p><br>");
        }
        //check Bnumber and get ID
        $DriverID=-1;
        $sql = "SELECT ID FROM RIDIC WHERE RODNECISLO = '$BNumber'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $DriverID = $result->fetch_assoc()["ID"];
        }else{
            die("<p>Řidič s tímto rodným číslem v systému neexistuje!</p><br>");
        }

        $sql = "SELECT ID FROM PRESTUPEK WHERE EVIDENCNICISLO = '$ENumber'";
        $result = $conn->query($sql);
         if ($result->num_rows > 0) {
            die("<p>Přestupek s tímto evidenčním číslem už v systému existuje!</p><br>");
        }
        //create and redirect
        $sql = "INSERT INTO PRESTUPEK(DATUMACAS,MISTO,EVIDENCNICISLO,PRESTUPEKTYPID) VALUES ('$date','$place','$ENumber','$type')";
        $conn->query($sql);
        $sql = "SELECT ID FROM PRESTUPEK WHERE EVIDENCNICISLO = '$ENumber'";
        $result = $conn->query($sql);
        $offenceId = $result->fetch_assoc()["ID"];

        //echo "$VehicleID   ";
        //echo "$DriverID  ";
        //echo "$offenceId";
        $sql = "INSERT INTO RIDICVOZIDLOPRESTUPEK(RIDICID,VOZIDLOID,PRESTUPEKID) VALUES ('$DriverID','$VehicleID','$offenceId')";
        $conn->query($sql);

        $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Nový Přestupek id= " . $offenceId . "', NOW())";
            $result = $conn->query($sql);

        header("Location: ?page=ProfilPrestupku&prestupek=" . $offenceId);
    }
}
  ?>