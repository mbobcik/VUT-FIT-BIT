<?php
    if(!isset($_GET["loc"]) || empty($_GET["loc"])){
        $loc = "vozidlo";
    }
    else{
        $loc = $_GET["loc"];
    }
?>

<div id="profilPage">
    <div id="profilMenu">
        <ul>
            <li><a href="?page=Vytvorit&loc=vozidlo" <?php if($loc === "vozidlo") echo "class=\"selected\""?>>vozidlo</a></li>
            <li><a href="?page=Vytvorit&loc=ridic" <?php if($loc === "ridic") echo "class=\"selected\""?>>řidiče</a></li>
            <li><a href="?page=Vytvorit&loc=prestupek" <?php if($loc === "prestupek") echo "class=\"selected\""?>>přestupek</a></li>
        </ul>
    </div>
    <div id="profilBody">
        
        <?php       
            require './Profil/nacteniRoleUzivatele.php';
        
            if($loc === "vozidlo"){
                require './novyZaznam/noveVozidloPage.php';
            } else if($loc === "ridic"){
                require './novyZaznam/novyRidicPage.php';
            }else if($loc === "prestupek"){
                require './novyZaznam/novyPrestupekPage.php';
            }      
        ?>

    </div>    
</div>
