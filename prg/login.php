<?php


// A függvénytároló betöltése (nincs rá szükség ha a .htaccess mûködik)
include("../includes/functions.inc.php");
/*
// Ha a referrer a registration.php, akkor sikeres volt a regisztráció
if (basename($_SERVER['HTTP_REFERER']) == "registration.php")
{$msgs[] = "Sikeres regisztráció!";}
*/

// Ha van bejövõ adat, akkor ellenõrizzük, és beléptetünk
if (count($_POST) > 0)
{
	// Megnézzük, hgy minden adat megvan-e, ha nincs, akkor a hibaüzeneteket listázzuk
	$errors = data_check($_POST);
	
	if (count($errors) == 0)
	{
		// Ha minden adat megvan, akkor megkeressük a felhasználót az adatfájlban,
		// és ha beléphet, akkor átirányítjuk a vendégkönyv oldalra
		//...ezen a ponton történhetne upgrade sütis, vagy session-ös megoldással
		$session_id = db_login($_POST);
		if ($session_id)
		{
			// Haddis menjen sütibe le
			setcookie("session_id", $session_id,0,'/iktato/');
			setcookie("user_name", $_POST['user'],0,'/iktato/');
			
			header("Location: ../menu/main.php");
		}
		else
		{
			$errors[] = "Hibás bejelentkezés!";
		}
	}
	else
	{
			$errors[] = "Nem tudom mi van!";
	}
}
	// Gondoskodunk róla, hogy magyar karakterkészlettel dolgozzon a kliens
	header("Content-type: text/html;charset=iso-8859-2");

	//HTML fejléc
    $header_text = CEGNEV." - Belépés";
    html_start(PRGNEV, $header_text);

	// Ha van hibaüzenet, akkor kiírjuk
	if(isset($errors) && count($errors) > 0)
	{
		display_error($errors);
	}

	// Ha van nyugtázó üzenet, akkor kiírjuk
	if(isset($msgs) && count($msgs) > 0)
	{
		display_msgs($msgs);
	}
		
	// Belépõ ûrlap
		// Mezõk összeállítása
		$form_fields = array(
							"Név:" => "user",
							"Jelszó:" => "pass"						
							);
		// Ûrlap kiírás
		draw_form("", $_SERVER['PHP_SELF'], "post", $form_fields);


		
		// Debug
/*
		foreach ($connection1->params as $kulcs => $ertek)
		{
			echo "$kulcs: $ertek<br>";
		}
*/		
	// HTML lábléc
	html_end();


?>