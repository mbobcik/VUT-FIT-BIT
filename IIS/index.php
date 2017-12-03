<?php
    session_start();  
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style.css">        
    </head>
    <body>

        <div id="page">
            
            <?php
            require_once './functions.php';
            $conn = connectDB();
            
            require './menu.php';
            
            if(!isset($_SESSION["uzivatelId"])){
                        require './LoginPage.php';  
                        die();
                    }
            
                if($_GET["page"] === "ProfilRidice")   {   
                    require './ProfilRidice/ProfilRidicePage.php';                
                } else if($_GET["page"] === "ProfilVozidla")   {  
                    require './ProfilVozidla/ProfilVozidlaPage.php';                
                } else if($_GET["page"] === "ProfilPrestupku")   { 
                    require './ProfilPrestupku/ProfilPrestupkuPage.php';                
                } else if($_GET["page"] === "Uzivatele")   { 
                    require './Uzivatele/UzivatelePage.php';                
                } else if($_GET["page"] === "VyhledavaniRidice")   {  
                    $_SESSION["vyhledavaniRidice"] = 1;
                    require './vyhledavani/vyhledavaniRidicePage.php';                
                } else if($_GET["page"] === "VyhledavaniVozidla")   {  
                    $_SESSION["vyhledavaniVozidla"] = 1;
                    require './vyhledavani/vyhledavaniVozidlaPage.php';                
                } else if($_GET["page"] === "Vyhledavani")   {  
                    require './vyhledavani/VyhledavaniPage.php';                
                }  else if($_GET["page"] === "login")   {  
                    require './LoginPage.php';                
                } else if($_GET["page"] === "chPass")   {  
                    require './Uzivatele/zmenHeslo.php';                
                }else if($_GET["page"] === "Vytvorit")   {  
                    require './novyZaznam/NovyZaznamPage.php';                
                } else{
                    header("Location: ?page=Vyhledavani");
                }
            ?>
            
        </div>


    </body>
</html>
