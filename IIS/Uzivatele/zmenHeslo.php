<?php
require_once './Profil/nacteniRoleUzivatele.php';

if ($role["ZMENAHESLAUZIVATELE"] != 1 && $role["ZMENAVLASTNIHOHESLA"] != 1)
    die("Nemáte právo měnit hesla.");


if (isset($_POST["chPassBtn"])) {
    $ret = chPass($_POST);
    $_POST = array();
    header("Location: ?page=chPass&ret=" . $ret);
}

$sql = "SELECT * FROM UZIVATEL WHERE ID=" . $_SESSION["uzivatelId"];
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    for($i = 0; $uzivatel = $result->fetch_assoc(); $i++){
        $uzivatele[$i]["JMENO"] = $uzivatel["JMENO"];
    }
}
//21232f297a57a5a743894a0e4a801fc3
//5cc32e366c87c4cb49e4309b75f57d64
//7ddb9545d033542c9b21b7b280e3a4d1

echo $_GET["ret"];
?>



<div>

    <form id="formZmenHeslo" method="post" action="?page=chPass">
        <table>
            <tr><td>Uživatel:</td><td><?php if ($role["ZMENAHESLAUZIVATELE"] != 1) echo $uzivatele[0]["JMENO"]?><input id="inputName" type="text" name="userName" onchange="onChangeName()" value="<?php echo $uzivatele[0]["JMENO"] ?>" <?php if ($role["ZMENAHESLAUZIVATELE"] != 1) echo "hidden"?>></td></tr>
            <tr><td>Nové heslo (min 8 znaků):</td><td><input id="inputPass" type="password" name="newPass" value=""></td></tr>
            <tr><td>Nové heslo znovu:</td><td><input id="inputPassA" type="password" name="newPassA" value=""></td></tr>
            <tr><td></td><td><input id="butZmen" type="submit" name="chPassBtn" value="Změnit"></td></tr>
        </table>        
    </form>




</div>