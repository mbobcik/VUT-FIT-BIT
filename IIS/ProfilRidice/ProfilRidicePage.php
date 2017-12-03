<?php
    if(!isset($_GET["loc"]) || empty($_GET["loc"])){
        $loc = "ridic";
    }
    else{
        $loc = $_GET["loc"];
    }
    
    if(isset($_GET["ridic"]) && !empty($_GET["ridic"])){
        $_SESSION["ridicId"] = $_GET["ridic"];
        header("Location: ?page=ProfilRidice");
    }
    
?>

<h1>Profil řidiče</h1>

<div id="profilPage">
    <div id="profilMenu">
        <ul>
            <li><a href="?page=ProfilRidice&loc=ridic" <?php if($loc === "ridic") echo "class=\"selected\""?>>Informace o řidiči</a></li>
            <li><a href="?page=ProfilRidice&loc=vozidla" <?php if($loc === "vozidla") echo "class=\"selected\""?>>Vlastnena vozidla</a></li>
            <li><a href="?page=ProfilRidice&loc=prestupky" <?php if($loc === "prestupky") echo "class=\"selected\""?>>Přestupky řidiče</a></li>
        </ul>
    </div>
    <div id="profilBody">
        
        <?php        
            require './Profil/nacteniRoleUzivatele.php';
        
            if($loc === "vozidla"){
                require './ProfilRidice/vozidla.php';
            } else if($loc === "prestupky"){
                require './ProfilRidice/prestupky.php';
            } else {
                require './ProfilRidice/oRidici.php';
            }        
        ?>

        
    </div>    
</div>

