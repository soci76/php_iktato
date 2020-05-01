<?php


// A f�ggv�nyt�rol� bet�lt�se (nincs r� sz�ks�g ha a .htaccess m�k�dik)
include("../includes/functions.inc.php");
/*
// Ha a referrer a registration.php, akkor sikeres volt a regisztr�ci�
if (basename($_SERVER['HTTP_REFERER']) == "registration.php")
{$msgs[] = "Sikeres regisztr�ci�!";}
*/

// Ha van bej�v� adat, akkor ellen�rizz�k, �s bel�ptet�nk
if (count($_POST) > 0)
{
	// Megn�zz�k, hgy minden adat megvan-e, ha nincs, akkor a hiba�zeneteket list�zzuk
	$errors = data_check($_POST);
	
	if (count($errors) == 0)
	{
		// Ha minden adat megvan, akkor megkeress�k a felhaszn�l�t az adatf�jlban,
		// �s ha bel�phet, akkor �tir�ny�tjuk a vend�gk�nyv oldalra
		//...ezen a ponton t�rt�nhetne upgrade s�tis, vagy session-�s megold�ssal
		$session_id = db_login($_POST);
		if ($session_id)
		{
			// Haddis menjen s�tibe le
			setcookie("session_id", $session_id,0,'/iktato/');
			setcookie("user_name", $_POST['user'],0,'/iktato/');
			
			header("Location: ../menu/main.php");
		}
		else
		{
			$errors[] = "Hib�s bejelentkez�s!";
		}
	}
	else
	{
			$errors[] = "Nem tudom mi van!";
	}
}
	// Gondoskodunk r�la, hogy magyar karakterk�szlettel dolgozzon a kliens
	header("Content-type: text/html;charset=iso-8859-2");

	//HTML fejl�c
    $header_text = CEGNEV." - Bel�p�s";
    html_start(PRGNEV, $header_text);

	// Ha van hiba�zenet, akkor ki�rjuk
	if(isset($errors) && count($errors) > 0)
	{
		display_error($errors);
	}

	// Ha van nyugt�z� �zenet, akkor ki�rjuk
	if(isset($msgs) && count($msgs) > 0)
	{
		display_msgs($msgs);
	}
		
	// Bel�p� �rlap
		// Mez�k �ssze�ll�t�sa
		$form_fields = array(
							"N�v:" => "user",
							"Jelsz�:" => "pass"						
							);
		// �rlap ki�r�s
		draw_form("", $_SERVER['PHP_SELF'], "post", $form_fields);


		
		// Debug
/*
		foreach ($connection1->params as $kulcs => $ertek)
		{
			echo "$kulcs: $ertek<br>";
		}
*/		
	// HTML l�bl�c
	html_end();


?>