<?php
if(isset($_POST["logout"])){
    session_unset();
    header("Location: ?page=login");    
}

if(isset($_POST["login"])){
    header("Location: ?page=login");
}

if(isset($_POST["chPass"])){
    header("Location: ?page=chPass");
}

if(isset($_SESSION["uzivatelId"])){
    $sql = "SELECT JMENO, ROLEID FROM UZIVATEL WHERE ID = " . $_SESSION["uzivatelId"];
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {   
        $u = $result->fetch_assoc();
        $uJmeno = $u["JMENO"];
        $sql = "SELECT NAZEVROLE FROM ROLE WHERE ID = " . $u["ROLEID"];
        $result = $conn->query($sql);
        if ($result->num_rows == 1){
            $r = $result->fetch_assoc();
            $uRole = $r["NAZEVROLE"];              
        }
        else{
            $uRole = "";     
            $uJmeno = "";
        }
    }
    else{
        $uRole = "";     
        $uJmeno = "";
    }
}
else{
    $uJmeno = "Nepřihlášen";
    $uRole = "";      
}



?>

<script>

    

</script>

<table>
    <form method="post" action="index.php">
        <tr><td><a href="index.php">Hlavní strana</a></td><td>Přihlášený uživatel:</td><td><?php echo $uJmeno . " (" . $uRole . ")"  ?></td>
        <td><?php if(isset($_SESSION["uzivatelId"])){ ?> 
            <input type="submit" name="logout" value="Odhlásit"></td>
        <td><input type="submit" name="chPass" value="Změnit heslo">
        <?php } else{ ?>
            <input type="submit" name="login" value="Přihlásit">            
        <?php } ?>
        </td></tr>
    </form>
</table>
    


