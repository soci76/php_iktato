<?php
include("../includes/functions.inc.php");

// Kiléptetés
setcookie("session_id");
setcookie("user_name");

// HTML fejléc
$header_text = CEGNEV." - Sikeres kilépés!";
html_start(PRGNEV, $header_text);
$links = array("Belépés" => "../prg/login.php");
?>

<br>
<?php draw_menu($links);?>
<br><br>

<?php
	html_end();
?>