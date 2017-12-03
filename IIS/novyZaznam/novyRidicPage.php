<?php if ($role["PRIDANIZAZNAMU"] != 1) die("Nemáte dostatečná práva"); ?>

<div id="udajeProfilu">
    <form  method="post" action="?page=Vytvorit&loc=ridic"  id="searchform"> 
      <table style="margin-left: 0; padding: 10px">
        <tr><td>Příjmení:</td><td> <input  type="text" name="surname"></td></tr>
        <tr><td>Jméno:</td><td><input  type="text" name="name"> </td></tr>
        <tr><td>Datum narození:</td><td><input  type="date" name="BDate"></td></tr>
        <tr><td>Rodné číslo(10 číslic):</td><td> <input  type="int" name="BNumber"> </td></tr>
        <tr><td>Ulice:</td><td><input  type="text" name="street"> </td></tr>
        <tr><td>Město:</td><td><input  type="text" name="city"> </td></tr>
        <tr><td>PSČ:</td><td><input  type="text" name="PSC"> </td></tr>
        <tr><td></td><td><input  type="submit" name="submit" value="Vytvořit" <?php if ($role["PRIDANIZAZNAMU"] != 1) echo "disabled"; ?>> </td></tr>
    </table>
</form> 
<p>*Povinná pole</p><br>

<?php
require_once 'functions.php';
if(isset($_POST['submit'])){
    $name=$_POST['name']; 
    $surname=$_POST['surname']; 
    $BNumber=$_POST['BNumber']; 
    $BDate=$_POST['BDate'];
    $street =  $_POST['street'];
    $city =  $_POST['city'];
    $PSC =  $_POST['PSC'];
    //echo $BNumber;
    if ($name == "" || $surname == "" || $BNumber == 0||$BDate == "" || $street == "" || $city == "" || $PSC == "") {
        echo "<p>Vyplňte všechna povinná pole!</p><br>";
    }else {
        if (strlen("$BNumber") != 10) {
            die("Rodné číslo nemá správnou velikost!");
        }
        //check if BNumber exists 
        $sql = "SELECT ID FROM RIDIC WHERE RODNECISLO = '$BNumber'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<p>Uživatel s tímto rodným číslem už v systému existuje!</p><br>";
        }else{
                //check if address exists and potentionally get its id
            $addressId = -1;
            $sql = "SELECT ID FROM ADRESA WHERE ULICE='$street' AND MESTO='$city' AND PSC='$PSC'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $addressId = $row["ID"];
                }
            }else{
                    //if adress not existed, create it, and get id
                $sql = "INSERT INTO ADRESA(ULICE,MESTO,PSC) VALUES ('$street','$city','$PSC')";
                $conn->query($sql);
                $sql = "SELECT ID FROM ADRESA WHERE ULICE='$street' AND MESTO='$city' AND PSC='$PSC'";
                $result = $conn->query($sql);
                $id = $result->fetch_assoc();
                $addressId = $id["ID"];
            }
            //create driver
            $sql= "INSERT INTO RIDIC(JMENO,PRIJMENI,RODNECISLO,DATUMNAROZENI,ADRESAID) VALUES ('$name','$surname',$BNumber,'$BDate','$addressId')";
            $conn->query($sql); 
            //redirect
            $sql = "SELECT ID FROM RIDIC WHERE RODNECISLO = '$BNumber'";

            $result = $conn->query($sql);
            $id = $result->fetch_assoc();
                //echo "ID=" . $id["ID"];

            $sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Nový Řidič id= " . $id["ID"] . "', NOW())";
            $result = $conn->query($sql);

            header("Location: ?page=ProfilRidice&ridic=" . $id["ID"]);
        }
    }
}
?>
<hr>
</div>
