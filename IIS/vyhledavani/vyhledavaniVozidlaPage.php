<div id="udajeProfilu">
    <form  method="post" action="?page=Vyhledavani&loc=vozidla"  id="searchform"> 
      <table style="margin-left: 0; padding: 10px">
         <tr><td>SPZ:</td><td><input  type="text" name="spz"> </td></tr>
         <tr><td>Značka:</td><td> <input  type="text" name="brand"></td></tr>
         <tr><td>Model:</td><td> <input  type="text" name="model"> </td></tr>
         <tr><td>Rok výroby:</td><td><input  type="int" name="year"></td></tr>
         <tr><td>Barva:</td><td><input  type="text" name="color"></td></tr>
         <tr><td>Ukradeno:</td><td><input type="hidden" name="stolen" value="0" /><input  type="checkbox" name="stolen" value="1"></td></tr>
         <tr><td></td><td><input  type="submit" name="submit" value="Hledej"> </td></tr>
     </table>
 </form> 
 <hr>
</div>

<?php 
require_once 'functions.php';
if(isset($_POST['submit'])){

    $spz = $_POST['spz']; 
    $brand = $_POST['brand']; 
    $model = $_POST['model']; 
    $year  = $_POST['year']; 
    $color = $_POST['color']; 
    $stolen= $_POST['stolen']; 

    $sql = "SELECT ID,SPZ,BARVA,ZNACKA,MODEL,ROKVYROBY,UKRADENO FROM VOZIDLO WHERE ";

    if ($spz !== "") {
        $sql .= "SPZ LIKE '%$spz%' AND ";
    }
    if ($color !== "") {
        $sql .= "BARVA LIKE '%$color%' AND ";
    }
    if ($brand !== "") {
        $sql .= "ZNACKA LIKE '%$brand%' AND ";
    }
    if ($model !== "") {
        $sql .= "MODEL LIKE '%$model%' AND ";
    }
    if ($year !== "") {
        $sql .= "ROKVYROBY LIKE '%$year%' AND ";
    }
    if ($stolen != 0) {
        $sql .= "UKRADENO = '$stolen' AND ";
    }
    $sql .= "1"; // toto oprav

//echo $sql;
    $result = connectDB()->query($sql);
    
    if ($result->num_rows > 0) {
        //echo "AHOJ";
        echo "<div class=\"vysledekVyhledavani\"><table class=\"vysledekVyhledavani\" style=\"margin-left: 5px; padding: 10px\">";
    
        printf("<tr style=\"background-color: #f5f5f5\"><td>SPZ</td><td>Značka</td><td>Model</td><td>Rok výroby</td><td>barva</td><td>Ukradeno</td><td></td></tr>");
    
        while ($row = $result->fetch_assoc()) {
    
            $stolenChckBox = "<input type=\"checkbox\" disabled=\"disabled\" ";
            $stolenChckBox .= ($row["UKRADENO"]) ? "checked=\"checked\"/>" : "/>" ;
    
            printf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%d</td><td>%s</td><td>%s</td><td><a href='?page=ProfilVozidla&vozidlo=%d'>></a></td></tr>",$row["SPZ"], $row["ZNACKA"], $row["MODEL"], $row["ROKVYROBY"],$row["BARVA"],$stolenChckBox, $row["ID"]);
        }
    
        echo "</table></div>";
        /* free result set */
        $result->free();
    
    } else {
        echo "<p>Dotazu neodpovida zadne vozidlo</p>";
    }
}
?>