<div id="udajeProfilu">
    <form  method="post" action="?page=Vyhledavani&loc=ridici"  id="searchform"> 
      <table style="margin-left: 0; padding: 10px">
       <tr><td>Příjmení:</td><td> <input  type="text" name="surname"></td></tr>
       <tr><td>Jméno:</td><td><input  type="text" name="name"> </td></tr>
       <tr><td>Datum narození:</td><td><input  type="date" name="BDate"></td></tr>
       <tr><td>Rodné číslo:</td><td> <input  type="int" name="BNumber"> </td></tr>
       <tr><td></td><td><input  type="submit" name="submit" value="Hledej"> </td></tr>
     </table>
   </form> 
   <hr>
</div>

<?php
require_once 'functions.php';
if(isset($_POST['submit'])){
  $name=$_POST['name']; 
  $surname=$_POST['surname']; 
  $BNumber=$_POST['BNumber']; 
  $BDate=$_POST['BDate']; 


  $sql = "SELECT ID,PRIJMENI,JMENO,RODNECISLO,DATUMNAROZENI FROM RIDIC WHERE ";
  //PRIJMENI= '$surname' OR JMENO = '$name' OR RODNECISLO='$BNumber' OR DATUMNAROZENI ='$BDate'"

if ($name !== "") {
    $sql .= "JMENO LIKE '%$name%' AND ";
}
if ($surname !== "") {
    $sql .= "PRIJMENI LIKE '%$surname%' AND ";
}
if ($BNumber !== "") {
    $sql .= "RODNECISLO LIKE '%$BNumber%' AND ";
}
if ($BDate !== "") {
    $sql .= "DATUMNAROZENI = '$BDate' AND ";
}
$sql .= "1"; // toto oprav

  //echo $sql . "<br>";
  $result = connectDB()->query($sql);
  //echo "pocet radku: ".$result->num_rows. "<br>";
  if ($result->num_rows > 0) {
  echo "<div class=\"vysledekVyhledavani\"><table class=\"vysledekVyhledavani\" style=\"margin-left: 5px; padding: 10px\">";
  printf("<tr style=\"background-color: #f5f5f5\"><td>Jméno</td><td>Příjmení</td><td>Rodné číslo</td><td>Datum narození</td><td></td></tr>");
    while ($row = $result->fetch_assoc()) {
        //printf ("%s %s - %d %s <a href='?page=ProfilRidice&ridic=%d'>></a><br>",$row["JMENO"], $row["PRIJMENI"], $row["RODNECISLO"], $row["DATUMNAROZENI"],$row["ID"]);

        printf("<tr><td>%s</td><td>%s</td><td>%d</td><td>%s</td><td><a href='?page=ProfilRidice&ridic=%d'>></a></td></tr>",$row["JMENO"], $row["PRIJMENI"], $row["RODNECISLO"], $row["DATUMNAROZENI"],$row["ID"]);
    }
 echo "</table></div>";
    /* free result set */
    $result->free();

} else {
   echo "<p>Dotazu neodpovida zadny ridic</p>";
}
}
?>