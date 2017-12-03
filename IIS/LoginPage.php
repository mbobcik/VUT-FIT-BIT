<?php
require_once './functions.php';
$conn = connectDB();

if(isset($_POST["login"])){
    $ret = login($_POST);
    $_POST = array(); 
    if($ret == "")
        header("Location: index.php");     
    else 
        header("Location: ?page=login&ret=" . $ret);     
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
    for($i = 0; $uzivatel = $result->fetch_assoc(); $i++){
        $uzivatele[$i]["JMENO"] = $uzivatel["JMENO"];
        $uzivatele[$i]["ROLEID"] = $uzivatel["ROLEID"];
    }
}

echo $_GET["ret"];

?>



<div>
    <script>
        function login(){            
            var form = document.getElementById("formLogIn");
            
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "login");
            hiddenField.setAttribute("value", "1");
            form.appendChild(hiddenField);

            form.submit();            
        }
        
    
    </script>
    
    
    <table>
        <form id="formLogIn" method="post" action="?page=login">
            <tr><td>Uživatelské jméno:</td><td><input id="inputName" type="text" name="userName"></td></tr>        
        <tr><td>Heslo: </td><td><input id="inputPass" type="password" name="pass"></td></tr> 
        <tr><td></td><td><input type="submit" value="Přihlásit" onclick="login()"></td></tr>  
        
    </table>
    
    
</div>   
