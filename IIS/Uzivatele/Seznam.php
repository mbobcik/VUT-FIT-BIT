<?php

if (isset($_POST["smazatUzivateleId"])) {
    $ret = smazatUzivatele($_POST["smazatUzivateleId"]);    
    $_POST = array();
    header("Location: ?page=Uzivatele&loc=seznam&ret=" . $ret); 
}

if (isset($_POST["upravRoliUzivateleId"])) {
    $ret = upravRoli($_POST["upravRoliUzivateleId"], $_POST["roleId"]);
    $_POST = array();
    header("Location: ?page=Uzivatele&loc=seznam&ret=" . $ret); 
}

$sql = "SELECT * FROM ROLE";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $seznamRoliInfo = $result;
} else {
    die("V systému nejsou vytvořeny žádné role uživatele. Prosím kontaktujte administrátora.");
}

for($j = 0; $mojeRole = $seznamRoliInfo->fetch_assoc(); $j++){
    $seznamRoli[$j] = $mojeRole;
} 

$sql = "SELECT * FROM UZIVATEL";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    for($i = 0; $uzivatel = $result->fetch_assoc(); $i++){   
        $uzivatele[$i]["ID"] = $uzivatel["ID"];
        $uzivatele[$i]["JMENO"] = $uzivatel["JMENO"];
        $uzivatele[$i]["ROLEID"] = $uzivatel["ROLEID"];
        
        for($j = 0; $seznamRoli[$j] != NULL; $j++){
            if($seznamRoli[$j]["ID"] == $uzivatele[$i]["ROLEID"]){
                $uzivatele[$i]["NAZEVROLE"] = $seznamRoli[$j]["NAZEVROLE"];
            }
        }     
    }
}

if($role["ZMENAPRAVUZIVATELE"] == 1)
    $zmenaPrav = "";
else
    $zmenaPrav = "disabled";

echo $_GET["ret"];

?>

<script>
function upravRoli(uzivatelId){
    var r = confirm("Skutečně chcete upravit roli tohoto uživatele?");
    if (r === true) {

        var form = document.getElementById("formUzivatele");

        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", "upravRoliUzivateleId");
        hiddenField.setAttribute("value", uzivatelId);
        form.appendChild(hiddenField);

        var hiddenField2 = document.createElement("input");
        hiddenField2.setAttribute("type", "hidden");
        hiddenField2.setAttribute("name", "roleId");
        hiddenField2.setAttribute("value", document.getElementById("select-" + uzivatelId).value);
        form.appendChild(hiddenField2);

        form.submit();
    }  
}

function smazUzivatele(uzivatelId){
    var r = confirm("Skutečně chcete smazat tohoto uživatele?");
    if (r === true) {

        var form = document.getElementById("formUzivatele");

        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", "smazatUzivateleId");
        hiddenField.setAttribute("value", uzivatelId);
        form.appendChild(hiddenField);

        form.submit();
    }
}

</script>

<div>
    <table>
        <form id="formUzivatele" method="post" action="?page=Uzivatele&loc=seznam">
        <tr class="tableHead"><td>Uživatelské jméno</td><td>Role</td><?php if($zmenaPrav === ""){ ?> <td></td> <?php }?><?php if($role["ODEBRANIUZIVATELE"] == 1){ ?> <td></td> <?php }?></tr>               
        <?php
            for($i = 0; $uzivatele[$i] != NULL; $i++){
                ?><tr class="tableBody"><td><?php echo $uzivatele[$i]["JMENO"] ?></td><td>
                        <select name="roleUzivatele" id="select-<?php echo $uzivatele[$i]["ID"] ?>" <?php echo $zmenaPrav?>>
                        <?php 
                            for($j = 0; $seznamRoli[$j] != NULL; $j++){
                                if($seznamRoli[$j]["ID"] == $uzivatele[$i]["ROLEID"])
                                    $selected = "selected";
                                else
                                    $selected = "";
                                echo "<option value=\"" . $seznamRoli[$j]["ID"] . "\" " . $selected . ">" . $seznamRoli[$j]["NAZEVROLE"] . "</option>";
                            }
                        ?>
                        </select></td>
                        <?php if($zmenaPrav === ""){ ?> <td><input type="button" value="Uložit roli" onclick="upravRoli(<?php echo $uzivatele[$i]["ID"] ?>)"></td> <?php }?>
                        <?php if($role["ODEBRANIUZIVATELE"] == 1){ ?> <td><input type="button" value="Smazat Uživatele" onclick="smazUzivatele(<?php echo $uzivatele[$i]["ID"] ?>)"></td> <?php }?>
                </tr>
                        
                        <?php
            }
        ?>   
        </form>
    </table>
    
    
    
    
    
</div>
