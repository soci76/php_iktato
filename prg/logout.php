<?php
include("../includes/functions.inc.php");

// Kil�ptet�s
setcookie("session_id");
setcookie("user_name");

// HTML fejl�c
$header_text = CEGNEV." - Sikeres kil�p�s!";
html_start(PRGNEV, $header_text);
$links = array("Bel�p�s" => "../prg/login.php");
?>

<br>
<?php draw_menu($links);?>
<br><br>

<?php
	html_end();
?>