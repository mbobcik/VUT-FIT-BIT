<div id="profilMenu">
    <ul>
        <li><a href="?page=Vyhledavani" <?php if ($_GET["page"] === "Vyhledavani") echo "class=\"selected\"" ?>>Vyhledávání</a></li>
        <li><a href="?page=Uzivatele" <?php if ($_GET["page"] === "Uzivatele") echo "class=\"selected\"" ?>>Správa uživatelů a historie</a></li>
        <li><a href="?page=Vytvorit" <?php if ($_GET["page"] === "Vytvorit") echo "class=\"selected\"" ?>>Vytvořit</a></li>
        <div id="menuspan" style="float: right"><?php require './appBar.php'; ?></div>
    </ul>
</div>
