<?php
/** F�rum alkalmaz�s
 * ****************** 
 * * F�ggv�nyt�rol� *
 * ****************** 
 * @version 1.0
 * @copyright 2004 Muzslay Andr�s
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

 
 
// A f�ggv�nyek �ltal haszn�lt glob�lis v�ltoz�k
//	$gb_data_file = "../data/gb_data.txt";
//	$login_file = "../data/login.txt";
	$style = "../style.css";
	   
// Az adatb�zis csatlakoz�s mindenk�ppen kell m�g a f�ggv�nyek el�tt...ha nem a hiba oldalon vagyunk
//*******************************************************************	
// A MySQL class include-ja
include("mysql.class.php");
include ("variables.php");
include ("confirm.php");

// A p�ld�nyos�t�s
$connection1 = new DB_Mysql("localhost", "dbusername", "dbpassword", "iktato");
/*
if (basename($_SERVER['PHP_SELF']) != "error.php")
{
 	// Csatlakoz�s
 	if (!$connection1->connect())
 	{
 		//echo "<p><font color='red'><b>".$connection1->params['error_msg']."</b></font></p>";
 		// �tir�ny�tunk a hiba oldalra
 		header("Location: error.php?status=1");
 	}
 	// Ha siker�lt csatlakozni, akkor kiv�lasztjuk az adatb�zist
 	elseif (!$connection1->select_db("kartyagyartas", $connection1->db_ci))
 	{
	 	// �tir�ny�tunk a hiba oldalra
	 	//header("Location: error.php?status=2");
	 	print_r($connection1->params['error_msg']);
 	}
 }
 */
//*******************************************************************
   //-----------------------------------------------------
// HTML kezd� f�ggv�ny, input: fejl�c sz�veg, oldal c�me
function html_start($title = "Strtigonium", $header = "Strigonium", $forras = "")
{
  // A st�lusf�jl helye glob�lis v�ltoz�b�l ker�l �tv�telre
  global $style;

  // A megfelel� karakterk�szlet kier�szakol�sa

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
// HTML z�r� f�ggv�ny
function html_end()
{
  echo "<br></td></tr>";
  echo "<tr><td align='center' class='footer'>".FOOTER_MSG."</td></tr></table>";
  echo "</body>\n</html>";
}

//------------------------------------------------------
// Men�kiir� f�ggv�ny
function draw_menu($_links)
{
  echo "<table class='link_table' align='center'><tr>";
  foreach ($_links as $_link => $_url){
    echo "<td class='link_cell'><a class='nah' href='$_url'>$_link</a></td>";
  }
  echo "</tr></table>";
}

//------------------------------------------------------
// Adatellen�rz� f�ggv�ny
function data_check($data)
{
  // Az esetleges hiba�zenetek ebben a t�mbben lesznek
  $errors = array();

  // 1. szint: adathi�ny
  //adathi�ny kiv�tel t�mb
  $lehetures = array("kod", "email","telefon","fax","allapot","megjegyzes", "status");
  foreach ($data as $index => $field){
    //kiv�telek
    if (in_array($index, $lehetures)){  }
      else{
      if (empty($field)){
        $errors[] = "Nincs meg minden sz�ks�ges adat!";
        return $errors;
      }
    }
  }

  // 2. szint: adathelyess�g - email c�mre
  if  (isset($data['email'])){
    if ($data['email']!="" && (!preg_match("/.+@.+\\..{2,4}/", $data['email']))){
      $errors[] = "Az e-mail c�m form�tuma nem megfelel�!";
    }
  }

  // 2. szint: adathelyess�g - jelsz� egyez�s�gre
  if (!empty($data['pass']) && !empty($data['pass2']) && ($data['pass'] != $data['pass2'])){
    $errors[] = "A 2 megadott jelsz� nem egyezik!";
  }

  // A hibat�mbbel t�r�nk vissza
  return $errors;
}
 
//------------------------------------------------------
// Legnagyobb mainnum ki�r�sa
function get_mainnum()
{
  // Kell az abatb�zis kapcsolat objektum
  global $connection1;
  
      $sql = "SELECT MAX(foszam) AS mfoszam FROM iktato";
      if ($connection1->db_query($sql)){
        if ($connection1->params['num_rows'] > 0){
          $gepek = $connection1->params['data'];
          foreach ($gepek as $rekord){
             return $rekord['mfoszam'];
            }
        }
        else{ // Ha nem j�tt vissza rekord, akkor hiba van
            return 0;
            } 
      }   
}
//------------------------------------------------------     
// Legnagyobb mainnum ki�r�sa
function get_igtnum($vfoszam)
{                                                   
  // Kell az abatb�zis kapcsolat objektum
  global $connection1;
  
      $sql = "SELECT MAX(alszam) AS malszam FROM iktato WHERE foszam='".$vfoszam."'";
      if ($connection1->db_query($sql)){
        if ($connection1->params['num_rows'] > 0){
          $gepek = $connection1->params['data'];
          foreach ($gepek as $rekord){
             return $rekord['malszam'];
            }
        }
        else{ // Ha nem j�tt vissza rekord, akkor hiba van
            return 0;
            } 
      }   
}
//------------------------------------------------------
// D�tum kiolvas�sa
function get_nowdate()
{
  $my_t=getdate(date("U"));
  return $my_t['year'].".".$my_t['mon'].".".$my_t['mday'];
}

//------------------------------------------------------
// A hozz�sz�l�st moder�l� f�ggv�ny
function comment_moderate($comment)
{
  // Kivessz�k a HTML tag-eket, csak a <B> maradhat
  $comment = strip_tags($comment, "<b></b><B></B>");

  // Kicser�lj�k a \n -eket <BR>-ekre...
  $comment = nl2br($comment);
  // Ide kell m�g a \n-ek kiszed�se
  $comment = preg_replace("/\n/", "", $comment);

  // Az esetleges ""-ekkel is gond lehet, kivessz�k
  $comment = addslashes($comment);

  return $comment;
}

//------------------------------------------------------
// A bel�ptet�st megval�s�t� f�ggv�ny
function db_login($login_data)
{
  // Kell az abatb�zis kapcsolat objektum
  global $connection1;

  // Megkeress�k a felhaszn�l�t az adatb�zisban
  $sql_str = "SELECT * FROM ugyintezo WHERE nev='".$login_data['user']."' AND jelszo='".$login_data['pass']."'";
  if ($connection1->db_query($sql_str)){
    $user_record = $connection1->params['data'];
    // Ha megtal�ltuk a felhaszn�l�t, �s csak 1 rekord j�tt le, akkor bel�phet, gener�lunk neki session_id-t
    if ($connection1->params['num_rows'] == 1){
      return md5($user_record[0]['nev'] . time());
    }
    else{ // Ha nem 1 rekord j�tt vissza, akkor hiba van
      return false;
    }
  }

  // Ha nem siker�lt megtal�lni a j� user-t �s a j� pass-t, akkor nem j�k a megadott adatok
  return false;

}
//------------------------------------------------------
// �rlap k�sz�t� f�ggv�ny
function draw_form($title = "", $action, $method = "POST", $form_fields = array(), $hidden_fields = array(),  $field_values = array(), $messages = array())
{

  // Kell az abatb�zis kapcsolat objektum
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

	// Ha comment a mez� neve, akkor t�bbsoros sz�vegbeviteli mez� kell, egy�bk�nt egysoros
	if ($field_name == "comment"){
          echo "<td><textarea name='$field_name' rows=10 cols=40 class='adat_mezo'>";
	  if (array_key_exists($field_name, $field_values)){echo $field_values[$field_name];}
	  echo "</textarea></td>\n";
	}
	elseif (preg_match("/pass/", $field_name) || $field_name=="jelszo"){
          echo "<td><input type='password' name='$field_name' class='adat_mezo'></td>\n";
	} // Ha dolgoz� leg�rd�l� men� kell
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
	    else{ // Ha nem j�tt vissza rekord, akkor hiba van
	    }
          }
	}// Ha irat_id leg�rd�l� men� kell
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
        else{ // Ha nem j�tt vissza rekord, akkor hiba van
            }
          }
 	}// Ha ugyfel_id leg�rd�l� men� kell
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
	    else{ // Ha nem j�tt vissza rekord, akkor hiba van
            }
          }
	}
	elseif ($field_name == "beosztas"){
          echo "<td><select name='".$field_name."' class='adat_mezo'>";
	  echo "<option value='titk�r'>titk�r</option>";
	  echo "<option value='irodavezet�'>irodavezet�</option>";
      echo "<option value='egy�b'>egy�b</option>";
	  echo "</select></td>";
	}
    elseif ($field_name == "szint"){
      echo "<td><select name='".$field_name."' class='adat_mezo'>";
      echo "<option value='1'>vend�g - 1</option>";
      echo "<option value='2'>felhaszn�l� - 2</option>";
      echo "<option value='3'>titk�r - 3</option>"; 
      echo "<option value='4'>irodavezet� - 4</option>"; 
      echo "<option value='5'>adminisztr�tor - 5</option>";
      echo "</select></td>";
    }
    elseif ($field_name == "irany"){
      echo "<td><select name='".$field_name."' class='adat_mezo'>";
      echo "<option value='bej�v�'>bej�v�</option>";
      echo "<option value='kimen�'>kimen�</option>";
      echo "<option value='egy�b'>egy�b</option>";
      echo "</select></td>";
    }
 	elseif ($field_name == "allapot"){
          echo "<td><select name='".$field_name."' class='adat_mezo'>";
	  echo "<option value='0'>passz�v</option>";
	  echo "<option value='1'>akt�v</option>";
	  echo "</select></td>";
	}
	elseif ($field_name == "vegrehajtott"){
          echo "<td><select name='".$field_name."' class='adat_mezo'>";
	  echo "<option value='1'>Folyamatban</option>";
	  echo "<option value='2'>Lez�rva</option>";
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
     // Az esetleges kapott param�tereket rejtett mez�kbe tessz�k
     foreach ($hidden_fields as $h_name => $h_value){
       echo "<input type='hidden' name='$h_name' value='$h_value'>";
     }
   ?>
  </form>
<?php
}
//------------------------------------------------------
// �rlap k�sz�t� f�ggv�ny 4(restore)
function draw_backupform($title = "", $action, $method = "POST")
{
  ?>
  <form action="<?php echo $action; ?>" method="<?php echo $method; ?>">
    <table class="form_table" align='center'>
    <?php
      echo "<tr><td colspan=2 class='form_title'>$title<br>&nbsp;</td></tr>";
      echo "<tr>\n";
      echo "<td class='cimke'>F�jln�v: </td>\n";
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
// Hiba�zenet ki�r� f�ggv�ny
function display_error($_errors = array())
{
  foreach ($_errors as $error_msg){
    echo "<center><font class='hiba'>$error_msg</font></center><br>";
  }
}

//------------------------------------------------------
// Nyugt�z� �zenet ki�r� f�ggv�ny
function display_msgs($_msgs = array())
{
  foreach ($_msgs as $_msg){
    echo "<center><font class='nyugtazas'>$_msg</font></center><br>";
  }
}

//------------------------------------------------------
// Az eddigi bejegyz�seket r�gz�t� f�ggv�ny
function draw_messages()
{
  global $gb_data_file;

  // A bejegyz�sek beolvas�sa
  $msgs = file($gb_data_file);
	
  // Kil�p�s link
  echo "<center><a href='gb_login.php' class='nah'>Kil�p�s</a></center>";
	
  echo "<hr width='400'>";
	
  foreach ($msgs as $msg){
    // Feldaraboljuk a megtiszt�tott sort
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
// A regisztr�lt felhaszn�l�t r�gz�t� f�ggv�ny
function user_reg($reg_data)
{
  // Kell az abatb�zis kapcsolat objektum
  global $connection1;

  // R�gz�t�s
  $sql_str = "INSERT INTO ugyintezo (nev, jelszo, teljes_nev, beosztas) VALUES('".trim($reg_data['user'])."', '".trim($reg_data['pass'])."', '".trim($reg_data['teljes_nev'])."','".trim($reg_data['beosztas'])."')";

  if ($connection1->db_query($sql_str)){
    return true;
  }
  else{
    return false;
  }
}

//------------------------------------------------------
// A m�r l�tez� user-es regisztr�ci�t kisz�r� f�ggv�ny
function user_exists($reg_data)
{
  // Kell az abatb�zis kapcsolat objektum
  global $connection1;

  // Megn�zz�k van-e m�r ilyen nev� felhaszn�l�nk
  $sql_str = "SELECT * FROM ugyintezo WHERE user_name='".trim($reg_data['user'])."'";

  if (($connection1->db_query($sql_str)) && ($connection1->params['num_rows'] == 1)){
    return true;
  }
  else{
    return false;
  }
}

//------------------------------------------------------
// A m�r l�tez� user-es regisztr�ci�t kisz�r� f�ggv�ny
function adatkeres($tabla,$mezo,$adat)
{
  // Kell az abatb�zis kapcsolat objektum
  global $connection1;

  // Megn�zz�k van-e m�r ilyen nev� felhaszn�l�nk
  $sql_str = "SELECT id FROM $tabla WHERE $mezo='".$adat."'";

  if (($connection1->db_query($sql_str)) && ($connection1->params['num_rows'] == 1)){
    return $connection1->params['data'][0]['id'];
  }
  else{
    return "";
  }
}

?>
