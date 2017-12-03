<?php
//TODO: zvyraznit aktualniho vlastnika
    if(!isset($_GET["loc"]) || empty($_GET["loc"])){
        $loc = "ridic";
    }
    else{
        $loc = $_GET["loc"];
    }

    if(isset($_GET["vozidlo"]) && !empty($_GET["vozidlo"])){
        $_SESSION["vozidloId"] = $_GET["vozidlo"];
        header("Location: ?page=ProfilVozidla");
    }
    
?>

<h1>Profil Vozidla</h1>

<div id="profilPage">
    <div id="profilMenu">
        <ul>
            <li><a href="?page=ProfilVozidla&loc=vozidlo" <?php if($loc === "vozidlo") echo "class=\"selected\""?>>Informace o vozidle</a></li>
            <li><a href="?page=ProfilVozidla&loc=vlastnici" <?php if($loc === "vlastnici") echo "class=\"selected\""?>>Historie vlastníků</a></li>
            <li><a href="?page=ProfilVozidla&loc=prestupky" <?php if($loc === "prestupky") echo "class=\"selected\""?>>Přestupky s vozidlem</a></li>
        </ul>
    </div>
    <div id="profilBody">
        
        <?php       
            require './Profil/nacteniRoleUzivatele.php';
        
            if($loc === "vlastnici"){
                require './ProfilVozidla/vlastnici.php';
            } else if($loc === "prestupky"){
                require './ProfilVozidla/prestupky.php';
            } else {
                require './ProfilVozidla/oVozidle.php';
            }        
        ?>

        
    </div>    
</div>

