<?php
//TODO: zvyraznit aktualniho vlastnika
    if(!isset($_GET["loc"]) || empty($_GET["loc"])){
        $loc = "ridic";
    }
    else{
        $loc = $_GET["loc"];
    }
    
    if(isset($_GET["prestupek"]) && !empty($_GET["prestupek"])){
        $_SESSION["prestupekId"] = $_GET["prestupek"];
        header("Location: ?page=ProfilPrestupku");
    }
    
?>

<h1>Profil Přestupku</h1>

<div id="profilPage">
    <div id="profilMenu">
        <ul>
            <li><a href="?page=ProfilPrestupku&loc=prestupek" <?php if($loc === "prestupek") echo "class=\"selected\""?>>Informace o přestupku</a></li>
            <li><a href="?page=ProfilPrestupku&loc=vinici" <?php if($loc === "vinici") echo "class=\"selected\""?>>Viníci</a></li>
        </ul>
    </div>
    <div id="profilBody">
        
        <?php       
            require './Profil/nacteniRoleUzivatele.php';
        
            if($loc === "vinici"){
                require './ProfilPrestupku/vinici.php';
            } else {
                require './ProfilPrestupku/oPrestupku.php';
            }        
        ?>

        
    </div>    
</div>

