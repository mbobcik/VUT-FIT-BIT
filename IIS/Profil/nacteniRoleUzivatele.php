<?php

require_once './functions.php';
$conn = connectDB();

$sql = "SELECT * FROM ROLE INNER JOIN UZIVATEL ON UZIVATEL.ROLEID = ROLE.ID WHERE UZIVATEL.ID = " . $_SESSION["uzivatelId"];
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $role = $result->fetch_assoc();
} else {
    die("Uživatel nemá platnou roli v systému.");
}

if ($role["ZOBRAZENIZAZNAMU"] != 1) {
    die("Uivatel nemá právo zobrazovat záznamy.");
}
?>