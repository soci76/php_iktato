<?php
/** Fórum alkalmazás
 * ****************** 
 * * Függvénytároló *
 * ****************** 
 * @version 1.0
 * @copyright 2004 Muzslay András
 *
 * Tartalom:
 * html_start()
 * html_end()
 * data_check()
 * comment_moderate()
 * draw_form()
 * display_error()
 * draw_messages()
 * user_reg()
 * user_exist()
 **/

 
 
// A függvények által használt globális változók
//	$gb_data_file = "../data/gb_data.txt";
//	$login_file = "../data/login.txt";
	$style = "../style.css";
	   
// Az adatbázis csatlakozás mindenképpen kell még a függvények elõtt...ha nem a hiba oldalon vagyunk
//*******************************************************************	
// A MySQL class include-ja
include("mysql.class.php");
include ("variables.php");
include ("confirm.php");

// A példányosítás
$connection1 = new DB_Mysql("localhost", "dbusername", "dbpassword", "iktato");
/*
if (basename($_SERVER['PHP_SELF']) != "error.php")
{
 	// Csatlakozás
 	if (!$connection1->connect())
 	{
 		//echo "<p><font color='red'><b>".$connection1->params['error_msg']."</b></font></p>";
 		// Átirányítunk a hiba oldalra
 		header("Location: error.php?status=1");
 	}
 	// Ha sikerült csatlakozni, akkor kiválasztjuk az adatbázist
 	elseif (!$connection1->select_db("kartyagyartas", $connection1->db_ci))
 	{
	 	// Átirányítunk a hiba oldalra
	 	//header("Location: error.php?status=2");
	 	print_r($connection1->params['error_msg']);
 	}
 }
 */
//*******************************************************************
   //-----------------------------------------------------
// HTML kezdõ függvény, input: fejléc szöveg, oldal címe
function html_start($title = "Strtigonium", $header = "Strigonium", $forras = "")
{
  // A stílusfájl helye globális változóból kerül átvételre
  global $style;

  // A megfelelõ karakterkészlet kierõszakolása

  header("Content-type: text/html;charset=iso-8859-2");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">                                                           
<html>
  <head>
  
    <?php if ($forras != "")  include ("../includes/jfunctions.js"); ?>
    <title><?php echo $title; ?></title>
      <link rel="stylesheet" href="<?php echo $style; ?>" type="text/css">
  </head>
  <?php 
    if ($forras == "")  echo "<body>";
    else  {
        switch ($forras) {
            case "item":
                echo "<body onload='Initcb(1)'>"; 
                break;                 
            case "get":
                echo "<body onload='Initcb(2)'>"; 
                break;                                 
            case "put":
                echo "<body onload='Initcb(3)'>"; 
                break;                                 
    }}?>
    <br>
      <table class="keret" align="center" cellspacing="0">
	<tr>
  	  <td class="header">
	    <div class="cimsor"><?php echo $header; ?></div>
	  </td>
	</tr>

	<tr>
  	  <td class="content">
<?php
}
//------------------------------------------------------
// HTML záró függvény
function html_end()
{
  echo "<br></td></tr>";
  echo "<tr><td align='center' class='footer'>".FOOTER_MSG."</td></tr></table>";
  echo "</body>\n</html>";
}

//------------------------------------------------------
// Menükiiró függvény
function draw_menu($_links)
{
  echo "<table class='link_table' align='center'><tr>";
  foreach ($_links as $_link => $_url){
    echo "<td class='link_cell'><a class='nah' href='$_url'>$_link</a></td>";
  }
  echo "</tr></table>";
}

//------------------------------------------------------
// Adatellenõrzõ függvény
function data_check($data)
{
  // Az esetleges hibaüzenetek ebben a tömbben lesznek
  $errors = array();

  // 1. szint: adathiány
  //adathiány kivétel tömb
  $lehetures = array("kod", "email","telefon","fax","allapot","megjegyzes", "status");
  foreach ($data as $index => $field){
    //kivételek
    if (in_array($index, $lehetures)){  }
      else{
      if (empty($field)){
        $errors[] = "Nincs meg minden szükséges adat!";
        return $errors;
      }
    }
  }

  // 2. szint: adathelyesség - email címre
  if  (isset($data['email'])){
    if ($data['email']!="" && (!preg_match("/.+@.+\\..{2,4}/", $data['email']))){
      $errors[] = "Az e-mail cím formátuma nem megfelelõ!";
    }
  }

  // 2. szint: adathelyesség - jelszó egyezõségre
  if (!empty($data['pass']) && !empty($data['pass2']) && ($data['pass'] != $data['pass2'])){
    $errors[] = "A 2 megadott jelszó nem egyezik!";
  }

  // A hibatömbbel térünk vissza
  return $errors;
}
 
//------------------------------------------------------
// Legnagyobb mainnum kiírása
function get_mainnum()
{
  // Kell az abatbázis kapcsolat objektum
  global $connection1;
  
      $sql = "SELECT MAX(foszam) AS mfoszam FROM iktato";
      if ($connection1->db_query($sql)){
        if ($connection1->params['num_rows'] > 0){
          $gepek = $connection1->params['data'];
          foreach ($gepek as $rekord){
             return $rekord['mfoszam'];
            }
        }
        else{ // Ha nem jött vissza rekord, akkor hiba van
            return 0;
            } 
      }   
}
//------------------------------------------------------     
// Legnagyobb mainnum kiírása
function get_igtnum($vfoszam)
{                                                   
  // Kell az abatbázis kapcsolat objektum
  global $connection1;
  
      $sql = "SELECT MAX(alszam) AS malszam FROM iktato WHERE foszam='".$vfoszam."'";
      if ($connection1->db_query($sql)){
        if ($connection1->params['num_rows'] > 0){
          $gepek = $connection1->params['data'];
          foreach ($gepek as $rekord){
             return $rekord['malszam'];
            }
        }
        else{ // Ha nem jött vissza rekord, akkor hiba van
            return 0;
            } 
      }   
}
//------------------------------------------------------
// Dátum kiolvasása
function get_nowdate()
{
  $my_t=getdate(date("U"));
  return $my_t['year'].".".$my_t['mon'].".".$my_t['mday'];
}

//------------------------------------------------------
// A hozzászólást moderáló függvény
function comment_moderate($comment)
{
  // Kivesszük a HTML tag-eket, csak a <B> maradhat
  $comment = strip_tags($comment, "<b></b><B></B>");

  // Kicseréljük a \n -eket <BR>-ekre...
  $comment = nl2br($comment);
  // Ide kell még a \n-ek kiszedése
  $comment = preg_replace("/\n/", "", $comment);

  // Az esetleges ""-ekkel is gond lehet, kivesszük
  $comment = addslashes($comment);

  return $comment;
}

//------------------------------------------------------
// A beléptetést megvalósító függvény
function db_login($login_data)
{
  // Kell az abatbázis kapcsolat objektum
  global $connection1;

  // Megkeressük a felhasználót az adatbázisban
  $sql_str = "SELECT * FROM ugyintezo WHERE nev='".$login_data['user']."' AND jelszo='".$login_data['pass']."'";
  if ($connection1->db_query($sql_str)){
    $user_record = $connection1->params['data'];
    // Ha megtaláltuk a felhasználót, és csak 1 rekord jött le, akkor beléphet, generálunk neki session_id-t
    if ($connection1->params['num_rows'] == 1){
      return md5($user_record[0]['nev'] . time());
    }
    else{ // Ha nem 1 rekord jött vissza, akkor hiba van
      return false;
    }
  }

  // Ha nem sikerült megtalálni a jó user-t és a jó pass-t, akkor nem jók a megadott adatok
  return false;

}
//------------------------------------------------------
// Ûrlap készítõ függvény
function draw_form($title = "", $action, $method = "POST", $form_fields = array(), $hidden_fields = array(),  $field_values = array(), $messages = array())
{

  // Kell az abatbázis kapcsolat objektum
  global $connection1;
  ?>
  <form name="foform" action="<?php echo $action; ?>" method="<?php echo $method; ?>">
    <table class="form_table" align='center'>
    <?php
      echo "<tr><td colspan=2 class='form_title'>$title<br>&nbsp;</td></tr>";
//      foreach ($messages as $_message){
//        echo "<tr><td colspan=2 class='message'>$_message</td></tr>";
//      }

      foreach ($form_fields as $label => $field_name){
        echo "<tr>\n";
	echo "<td class='cimke'>$label </td>\n";

	// Ha comment a mezõ neve, akkor többsoros szövegbeviteli mezõ kell, egyébként egysoros
	if ($field_name == "comment"){
          echo "<td><textarea name='$field_name' rows=10 cols=40 class='adat_mezo'>";
	  if (array_key_exists($field_name, $field_values)){echo $field_values[$field_name];}
	  echo "</textarea></td>\n";
	}
	elseif (preg_match("/pass/", $field_name) || $field_name=="jelszo"){
          echo "<td><input type='password' name='$field_name' class='adat_mezo'></td>\n";
	} // Ha dolgozó legördülõ menü kell
	elseif ($field_name == "ugyintezo_id"){
	  $sql = "SELECT id, teljes_nev FROM ugyintezo ORDER BY nev";
	  if ($connection1->db_query($sql)){
	    if ($connection1->params['num_rows'] > 0){
	      $dolgozok = $connection1->params['data'];
	      echo "<td><select name='".$field_name."' class='adat_mezo' selectedindex='2'>";
	      foreach ($dolgozok as $rekord){
                if ($rekord["id"] == $field_values["ugyintezo_id"]){ echo "<option selected value="; }
                  else{ echo "<option value="; }
	        echo $rekord['id'].">".$rekord['teljes_nev']."</option>";
              }
	      echo "</select></td>";
            }
	    else{ // Ha nem jött vissza rekord, akkor hiba van
	    }
          }
	}// Ha irat_id legördülõ menü kell
    elseif ($field_name == "irat_id"){
      $sql = "SELECT id, tipus, megjegyzes FROM irat";
      if ($connection1->db_query($sql)){
        if ($connection1->params['num_rows'] > 0){
          $gepek = $connection1->params['data'];
          echo "<td><select name='".$field_name."' class='adat_mezo' selectedindex='2'>";
          foreach ($gepek as $rekord){
                if ($rekord["id"] == $field_values["irat_id"]){ echo "<option selected value="; }
                  else{ echo "<option value="; }
                echo $rekord['id'].">".$rekord['tipus']." - ".$rekord['megjegyzes']."</option>";
              }
          echo "</select></td>";
            }
        else{ // Ha nem jött vissza rekord, akkor hiba van
            }
          }
 	}// Ha ugyfel_id legördülõ menü kell
	elseif ($field_name == "ugyfel_id"){
	  $sql = "SELECT id, nev FROM ugyfel";
	  if ($connection1->db_query($sql)){
	    if ($connection1->params['num_rows'] > 0){
	      $gepek = $connection1->params['data'];
	      echo "<td><select name='".$field_name."' class='adat_mezo' selectedindex='2'>";
	      foreach ($gepek as $rekord){
                if ($rekord["id"] == $field_values["ugyfel_id"]){ echo "<option selected value="; }
                  else{ echo "<option value="; }
                echo $rekord['id'].">".$rekord['nev']."</option>";
              }
	      echo "</select></td>";
            }
	    else{ // Ha nem jött vissza rekord, akkor hiba van
            }
          }
	}
	elseif ($field_name == "beosztas"){
          echo "<td><select name='".$field_name."' class='adat_mezo'>";
	  echo "<option value='titkár'>titkár</option>";
	  echo "<option value='irodavezetõ'>irodavezetõ</option>";
      echo "<option value='egyéb'>egyéb</option>";
	  echo "</select></td>";
	}
    elseif ($field_name == "szint"){
      echo "<td><select name='".$field_name."' class='adat_mezo'>";
      echo "<option value='1'>vendég - 1</option>";
      echo "<option value='2'>felhasználó - 2</option>";
      echo "<option value='3'>titkár - 3</option>"; 
      echo "<option value='4'>irodavezetõ - 4</option>"; 
      echo "<option value='5'>adminisztrátor - 5</option>";
      echo "</select></td>";
    }
    elseif ($field_name == "irany"){
      echo "<td><select name='".$field_name."' class='adat_mezo'>";
      echo "<option value='bejövõ'>bejövõ</option>";
      echo "<option value='kimenõ'>kimenõ</option>";
      echo "<option value='egyéb'>egyéb</option>";
      echo "</select></td>";
    }
 	elseif ($field_name == "allapot"){
          echo "<td><select name='".$field_name."' class='adat_mezo'>";
	  echo "<option value='0'>passzív</option>";
	  echo "<option value='1'>aktív</option>";
	  echo "</select></td>";
	}
	elseif ($field_name == "vegrehajtott"){
          echo "<td><select name='".$field_name."' class='adat_mezo'>";
	  echo "<option value='1'>Folyamatban</option>";
	  echo "<option value='2'>Lezárva</option>";
	  echo "</select></td>";
	}
    elseif ($field_name == "foszam"){
       echo "<td><input type='text' name='$field_name' class='adat_mezo' readonly='readonly' value='";
     if (array_key_exists($field_name, $field_values))
       { echo $field_values[$field_name];}
         echo "'></td>\n";
    }
    elseif ($field_name == "alszam"){
       echo "<td><input type='text' name='$field_name' class='adat_mezo' readonly='readonly' value='";
     if (array_key_exists($field_name, $field_values))
       { echo $field_values[$field_name];}
         echo "'></td>\n";
    }
 
       /*
       elseif (preg_match("/user/", $field_name)){
	 echo "<td>$user_name</td>";
       }*/
       else{
	 echo "<td><input type='text' name='$field_name' class='adat_mezo' value='";
	 if (array_key_exists($field_name, $field_values))
	   { echo $field_values[$field_name];}
         echo "'></td>\n";
        }
	echo "</tr>\n";
      }
      ?>
      <tr>
	<td colspan=2>&nbsp;</td>
      </tr>
      <tr>
	<td colspan=2 align="center">
	<input type="submit" value="OK" class='gomb'>
        </td>
      </tr>
    </table>
   <?php
     // Az esetleges kapott paramétereket rejtett mezõkbe tesszük
     foreach ($hidden_fields as $h_name => $h_value){
       echo "<input type='hidden' name='$h_name' value='$h_value'>";
     }
   ?>
  </form>
<?php
}
//------------------------------------------------------
// Ûrlap készítõ függvény 4(restore)
function draw_backupform($title = "", $action, $method = "POST")
{
  ?>
  <form action="<?php echo $action; ?>" method="<?php echo $method; ?>">
    <table class="form_table" align='center'>
    <?php
      echo "<tr><td colspan=2 class='form_title'>$title<br>&nbsp;</td></tr>";
      echo "<tr>\n";
      echo "<td class='cimke'>Fájlnév: </td>\n";
      echo "<td><input type='text' name='filename' class='adat_mezo' value='";
      echo gmdate('Ymd').".sql.gz'></td>\n";
      echo "</tr>\n";
      ?>
      <tr>
    <td colspan=2>&nbsp;</td>
      </tr>
      <tr>
    <td colspan=2 align="center">
    <input type="submit" value="OK" class='gomb'>
        </td>
      </tr>
    </table>
  </form>
<?php
}
//------------------------------------------------------
// Hibaüzenet kiíró függvény
function display_error($_errors = array())
{
  foreach ($_errors as $error_msg){
    echo "<center><font class='hiba'>$error_msg</font></center><br>";
  }
}

//------------------------------------------------------
// Nyugtázó üzenet kiíró függvény
function display_msgs($_msgs = array())
{
  foreach ($_msgs as $_msg){
    echo "<center><font class='nyugtazas'>$_msg</font></center><br>";
  }
}

//------------------------------------------------------
// Az eddigi bejegyzéseket rögzítõ függvény
function draw_messages()
{
  global $gb_data_file;

  // A bejegyzések beolvasása
  $msgs = file($gb_data_file);
	
  // Kilépés link
  echo "<center><a href='gb_login.php' class='nah'>Kilépés</a></center>";
	
  echo "<hr width='400'>";
	
  foreach ($msgs as $msg){
    // Feldaraboljuk a megtisztított sort
    list($user, $email, $comment, $date_time) = split("::", trim($msg));
	
    ?>
      <br>
	<table class='vk_table' align='center' cellpadding="3">
	  <tr>
            <td class='nev_cella'>
	      <a href="mailto:<?php echo $email; ?>" class="nah2">
	      <?php echo $user; ?></a>
            </td>
	    <td class='nev_cella' align='right'>
	      <?php echo $date_time; ?>
            </td>
          </tr>
	  <tr>
	    <td colspan=2 class='comment_cella'>
	      <?php echo $comment; ?>
	    </td>
	  </tr>
	</table>
      <br>
    <?php
  }
}

//------------------------------------------------------
// A regisztrált felhasználót rögzítõ függvény
function user_reg($reg_data)
{
  // Kell az abatbázis kapcsolat objektum
  global $connection1;

  // Rögzítés
  $sql_str = "INSERT INTO ugyintezo (nev, jelszo, teljes_nev, beosztas) VALUES('".trim($reg_data['user'])."', '".trim($reg_data['pass'])."', '".trim($reg_data['teljes_nev'])."','".trim($reg_data['beosztas'])."')";

  if ($connection1->db_query($sql_str)){
    return true;
  }
  else{
    return false;
  }
}

//------------------------------------------------------
// A már létezõ user-es regisztrációt kiszûrõ függvény
function user_exists($reg_data)
{
  // Kell az abatbázis kapcsolat objektum
  global $connection1;

  // Megnézzük van-e már ilyen nevû felhasználónk
  $sql_str = "SELECT * FROM ugyintezo WHERE user_name='".trim($reg_data['user'])."'";

  if (($connection1->db_query($sql_str)) && ($connection1->params['num_rows'] == 1)){
    return true;
  }
  else{
    return false;
  }
}

//------------------------------------------------------
// A már létezõ user-es regisztrációt kiszûrõ függvény
function adatkeres($tabla,$mezo,$adat)
{
  // Kell az abatbázis kapcsolat objektum
  global $connection1;

  // Megnézzük van-e már ilyen nevû felhasználónk
  $sql_str = "SELECT id FROM $tabla WHERE $mezo='".$adat."'";

  if (($connection1->db_query($sql_str)) && ($connection1->params['num_rows'] == 1)){
    return $connection1->params['data'][0]['id'];
  }
  else{
    return "";
  }
}

?>
