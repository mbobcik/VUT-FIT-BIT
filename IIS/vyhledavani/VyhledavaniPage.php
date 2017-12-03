<?php
    if(!isset($_GET["loc"]) || empty($_GET["loc"])){
        $loc = "vozidla";
    }
    else{
        $loc = $_GET["loc"];
    }
?>

<div id="profilPage">
    <div id="profilMenu">
        <ul>
            <li><a href="?page=Vyhledavani&loc=vozidla" <?php if($loc === "vozidla") echo "class=\"selected\""?>>v databázi vozidel</a></li>
            <li><a href="?page=Vyhledavani&loc=ridici" <?php if($loc === "ridici") echo "class=\"selected\""?>>v databázi řidičů</a></li>
        </ul>
    </div>
    <div id="profilBody">
        
        <?php       
            require './Profil/nacteniRoleUzivatele.php';
        
            if($loc === "vozidla"){
                require './vyhledavani/vyhledavaniVozidlaPage.php';
            } else if($loc === "ridici"){
                require './vyhledavani/vyhledavaniRidicePage.php';
            }      
        ?>

        
    </div>    
</div>
