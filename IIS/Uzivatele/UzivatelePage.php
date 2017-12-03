<?php
//TODO: zvyraznit aktualniho vlastnika
    if(!isset($_GET["loc"]) || empty($_GET["loc"])){
        $loc = "seznam";
    }
    else{
        $loc = $_GET["loc"];
    }
       
?>


<div id="profilPage">
    <div id="profilMenu">
        <ul>
            <li><a href="?page=Uzivatele&loc=seznam" <?php if($loc === "seznam") echo "class=\"selected\""?>>Správa uživatelů</a></li>
            <li><a href="?page=Uzivatele&loc=novy" <?php if($loc === "novy") echo "class=\"selected\""?>>Nový uživatel</a></li>
            <li><a href="?page=Uzivatele&loc=historie" <?php if($loc === "historie") echo "class=\"selected\""?>>Historie</a></li>
        </ul>
    </div>
    <div id="profilBody">
        
        <?php       
            require './Profil/nacteniRoleUzivatele.php';
        
            if($loc === "novy"){
                require './Uzivatele/novyUzivatel.php';
            } else if($loc === "historie"){
                require './Uzivatele/historie.php';
            } else {
                require './Uzivatele/Seznam.php';
            }        
        ?>

        
    </div>    
</div>

