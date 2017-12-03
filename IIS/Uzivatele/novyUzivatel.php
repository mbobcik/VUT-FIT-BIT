<?php
if (isset($_POST["novyUzivatel"])) {
    $ret = novyUzivatel($_POST);
    $_POST = array();
    header("Location: ?page=Uzivatele&loc=novy&ret=" . $ret);
}

if ($role["PRIDANIUZIVATELE"] != 1) {
    die("Nemáte dostatečná práva na založení nového uživatele.");
}

$sql = "SELECT * FROM ROLE";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $seznamRoli = $result;
} else {
    die("V systému nejsou vytvořeny žádné role uživatele. Prosím kontaktujte administrátora.");
}

$sql = "SELECT * FROM UZIVATEL";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    for ($i = 0; $uzivatel = $result->fetch_assoc(); $i++) {
        $uzivatele[$i]["JMENO"] = $uzivatel["JMENO"];
        $uzivatele[$i]["ROLEID"] = $uzivatel["ROLEID"];
    }
}

echo $_GET["ret"];
?>



<div>
    <script>
        function vytvorUzivatele() {

            onChangeName();
            onChangePass();

            if (document.getElementById("butVytvor").disabled == "disabled")
                return;

            var form = document.getElementById("formNovyUzivatel");

            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "novyUzivatel");
            hiddenField.setAttribute("value", "1");
            form.appendChild(hiddenField);

            form.submit();

        }

        function onChangeName() {
            var name = document.getElementById("inputName").value;

            var uzivatele = JSON.parse('<?php echo json_encode($uzivatele); ?>');

            var existuje = false;

            for (var i = 0; i < uzivatele.length; i++) {
                if (uzivatele[i]["JMENO"] == name) {
                    existuje = true;
                }
            }

            if (existuje) {
                document.getElementById("inputName").style.backgroundColor = "red";
                document.getElementById("butVytvor").disabled = 'disabled';
            } else {
                document.getElementById("inputName").style.backgroundColor = "white";
                document.getElementById("butVytvor").disabled = '';
            }
        }

        function onChangePass() {
            var pass = document.getElementById("inputPass").value;
            var passA = document.getElementById("inputPassA").value;

            var chyba = false;
            var chybaA = false;

            if (pass.length < 8)
                chyba = true;

            if (pass != passA)
                chybaA = true;


            if (chyba) {
                document.getElementById("inputPass").style.backgroundColor = "red";
            } else {
                document.getElementById("inputPass").style.backgroundColor = "white";
            }

            if (chybaA) {
                document.getElementById("inputPassA").style.backgroundColor = "red";
            } else {
                document.getElementById("inputPassA").style.backgroundColor = "white";
            }

            if (chyba || chybaA)
                document.getElementById("butZmen").disabled = 'disabled';
            else
                document.getElementById("butVytvor").disabled = '';

        }

    </script>


    <table>
        <form id="formNovyUzivatel" method="post" action="?page=Uzivatele&loc=novy">
            <tr><td>Uživatelské jméno:</td><td><input id="inputName" type="text" name="userName" onchange="onChangeName()"></td></tr>        
            <tr><td>Heslo (min. 8 znaků): </td><td><input id="inputPass" type="password" name="pass" onchange="onChangePass()"></td></tr>        
            <tr><td>Heslo znova: </td><td><input id="inputPassA" type="password" name="passAgain" onchange="onChangePass()"></td></tr>        
            <tr><td>Role</td><td><select name="role">
<?php
for ($i = 0; $jednaRole = $seznamRoli->fetch_assoc(); $i++) {
    echo "<option value=\"" . $jednaRole["ID"] . "\">" . $jednaRole["NAZEVROLE"] . "</option>";
}
?>
                    </select></td></tr>  
            <tr><td></td><td><input id="butVytvor" type="button" value="Vytvořit" onclick="vytvorUzivatele()"></td></tr>  

    </table>





</div>
