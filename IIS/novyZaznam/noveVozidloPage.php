<?php if ($role["PRIDANIZAZNAMU"] != 1) die("Nemáte dostatečná práva"); ?>

<div id="udajeProfilu">
    <form  method="post" action="?page=Vytvorit&loc=vozidlo"  id="searchform"> 
      <table style="margin-left: 0; padding: 10px">
       <tr><td>SPZ*:</td><td><input  type="text" name="spz"> </td></tr>
       <tr><td>Značka*:</td><td> <input  type="text" name="brand"></td></tr>
       <tr><td>Model*:</td><td> <input  type="text" name="model"> </td></tr>
       <tr><td>Rok výroby*:</td><td><input  type="int" name="year"></td></tr>
       <tr><td>Barva*:</td><td><input  type="text" name="color"></td></tr>

       <tr><td>Skupina*:</td><td>                          
          <select name="group">
            <?php 

            $sql = "SELECT ID,NAZEV FROM SKUPINA";
            $skupiny = $conn->query($sql);
              while ($row = $skupiny->fetch_assoc()) {
                  echo "<option value=\"".$row["ID"]."\">".$row["NAZEV"]."</option>";
              }
             ?>
       </td></tr>

       <tr><td>Ukradeno:</td><td><input type="hidden" name="stolen" value="0" /><input  type="checkbox" name="stolen" value="1"></td></tr>
       <tr><td></td><td><input  type="submit" name="submit" value="Vytvořit" <?php if ($role["PRIDANIZAZNAMU"] != 1) echo "disabled"; ?>> </td></tr>
   </table>
</form> 
<p>*Povinná pole</p><br>


<?php 
require_once 'functions.php';
if(isset($_POST['submit'])){

    $spz = $_POST['spz']; 
    $brand = $_POST['brand']; 
    $model = $_POST['model']; 
    $year  = $_POST['year']; 
    $color = $_POST['color']; 
    $stolen= $_POST['stolen']; 
    $group = $_POST['group']; 

   //echo "\"$spz\"<br>";
   //echo "\"$brand\"<br>";
   //echo "\"$model\"<br>";
   //echo "\"$year\"<br>";
   //echo "\"$color\"<br>";
   //echo "\"$stolen\"<br>";

    if ($spz == "" || $model == "" ||$year == 0||$color == "" || $group == 0) {
        echo "<p>Vyplňte všechna povinná pole!</p><br>";
    }else{
        $sql = "SELECT ID FROM VOZIDLO WHERE SPZ = '$spz'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<p>Auto s touto SPZ již v systému existuje!</p><br>";
        }else{
          $sql = "INSERT INTO VOZIDLO(SPZ, BARVA, ZNACKA,MODEL,ROKVYROBY,SKUPINAID,UKRADENO) VALUES ('$spz','$color','$brand','$model','$year','$group','$stolen')";
          $conn->query($sql);
          $sql = "SELECT ID FROM VOZIDLO WHERE SPZ = '$spz'";
          $result = $conn->query($sql);
          $id = $result->fetch_assoc();

$sql = "INSERT INTO HISTORIE VALUES (0, " . $_SESSION["uzivatelId"] . ", 'Nové Vozidlo id= " . $id["ID"] . "', NOW())";
            $result = $conn->query($sql);

          header("Location: ?page=ProfilVozidla&vozidlo=" . $id["ID"]);
        }
    }
  }
    ?>
    <hr>
</div>