<?php
include ("../includes/functions.inc.php");
$rlevel=3; //olvas�si szint
$wlevel=3; //�r�si szint -itt nem haszn�lt   
$dlevel=3; //t�rl�si szint  -itt nem haszn�lt   
$msg = ""; 

//include("includes/jfunctions.js");
// ($_SERVER['HTTP_REFERER'] != "login.php") && 
if (empty($_COOKIE['session_id']))
{
	header("Location: ../prg/login.php");
}
else
{
// A felhaszn�l�i jogok lek�rdez�se
    $user_sql = "SELECT id, szint FROM ugyintezo WHERE nev='".$_COOKIE['user_name']."'";
    
   if ($connection1->db_query($user_sql)){
       $dolgozo_id = $connection1->params['data'][0]['id'];
     $level = $connection1->params['data'][0]['szint'];
   }
    // bel�p�si szint ellen�rz�s
   if ($level<$rlevel) header("Location: ../menu/main.php?msg=1");

  // Szerkeszt�s megval�s�t�sa
  if (isset($_GET['id'])){
    // Szerkeszt�s megval�s�t�sa
    if (isset($_GET['action']) && $_GET['action'] == "edit"){

      $edit_sql = "SELECT * FROM ugyfel WHERE id='".$_GET['id']."'";
      $connection1->db_query($edit_sql);
                                                       
      if (count($connection1->params['data']) > 0){
        $id = $connection1->params['data'][0]['id'];
        $nev = $connection1->params['data'][0]['nev'];
        $irsz = $connection1->params['data'][0]['irsz'];
        $varos = $connection1->params['data'][0]['varos'];
        $utca = $connection1->params['data'][0]['utca'];
        $hazszam = $connection1->params['data'][0]['hazszam'];
        $email = $connection1->params['data'][0]['email'];
        $telefon = $connection1->params['data'][0]['telefon'];
        $fax = $connection1->params['data'][0]['fax'];               
        $allapot = $connection1->params['data'][0]['allapot'];
        $display_values = true;
      }
    }
  } // -- Szerkeszt�s v�ge

  // INSERT �S UPDATE megval�s�t�sa                                                                                                                                 
  if (count($_POST) > 0){

    $errors = data_check($_POST);

    if (count($errors) == 0){
      //UPDATE                                                                                                      
      if (!empty($_POST["id"])){
        $update_sql = "UPDATE ugyfel SET nev='".$_POST['nev']."', irsz='".$_POST['irsz']."', varos='".$_POST['varos']."', utca='".$_POST['utca']."', hazszam='".$_POST['hazszam']."', email='".$_POST['email']."', telefon='".$_POST['telefon']."', fax='".$_POST['fax']."', allapot='".$_POST['allapot']."' WHERE id='".$_POST['id']."'";
        $connection1->db_query($update_sql);
        if ($connection1->params['affected_rows'] == 1){
          $msg = "M�dos�t�s rendben!";
        }
        else{
          $msg = "M�dosit�si hiba! ".$update_sql;
        }
      }
      //INSERT
      else{                                                                                                                                           
        $insert_sql = "INSERT INTO ugyfel (nev,irsz,varos,utca,hazszam,email,telefon,fax,allapot) VALUES('".$_POST['nev']."','".$_POST['irsz']."','".$_POST['varos']."','".$_POST['utca']."','".$_POST['hazszam']."','".$_POST['email']."','".$_POST['telefon']."','".$_POST['fax']."','".$_POST['allapot']."')";
        $connection1->db_query($insert_sql);
        if ($connection1->params['affected_rows'] == 1){
          $msg = "Besz�r�s rendben!";
        }
        else{
          $msg = "Besz�r�si hiba! ".$insert_sql;
        }
      }
    }
    else{
      $msg = "Hiba t�rt�n a besz�r�s/m�dos�t�s k�zben! ".$errors[0];
    }
  } // UPDATE, INSERT v�ge

  // Az adatok lek�rdez�se
  $megrendeles_sql = "SELECT * FROM ugyfel";
  $connection1->db_query($megrendeles_sql);
  if ($connection1->params['num_rows'] > 0) $adatok = $connection1->params['data'];
  else $adatok = array();                           

  $header_cells = array("Id", "N�v", "Ir�ny�t�sz�m", "V�ros", "Utca", "H�zsz�m", "E-mail", "Telefon", "Fax","St�tusz");
  $links = array( "F�men�"               => "../menu/main.php",
                  "F�sz�m iktat�s"       => "../prg/p_mainnum.php",
                  "�gyf�l kezel�s"       => "../prg/p_contact.php");

  // HTML fejl�c
  // html_start("K�rtyagy�rt�s",$_COOKIE['user_name']." ($beosztas) - ".$header_text);
  $header_text = CEGNEV." - Egyszer� adatb�zisok";
  html_start(PRGNEV, $header_text);


  /*
echo "<pre>";
print_r($connection1->params['data']);
echo "</pre>";
*/
  ?>
  <br>
  <?php draw_menu($links);?>
  <br><br>
  <?php // echo $msg; ?>
  <table align="center" border="0" cellspacing="1" cellpadding="4" width="80%" class="data_table">
    <tr>
      <?php foreach ($header_cells as $cell){ echo "<th class='cell2'>$cell</th>";}?>
    </tr>
  <?php
  if ($connection1->params['num_rows'] > 0){
    foreach ($adatok as $rekord){
      foreach ($rekord as $key => $value){
        if (empty($value)) {$rekord[$key] = "&nbsp;";}
      }
      echo "<tr>";
      echo "<td class='data_cell'><a href='".$_SERVER['PHP_SELF']."?id=".$rekord['id']."&action=edit'>".$rekord['id']."</a></td>";
      echo "<td class='data_cell'>".$rekord['nev']."</td>";
      echo "<td class='data_cell'>".$rekord['irsz']."</td>";
      echo "<td class='data_cell'>".$rekord['varos']."</td>";
      echo "<td class='data_cell'>".$rekord['utca']."</td>";
      echo "<td class='data_cell'>".$rekord['hazszam']."</td>";
      echo "<td class='data_cell'>".$rekord['email']."</td>";
      echo "<td class='data_cell'>".$rekord['telefon']."</td>";
      echo "<td class='data_cell'>".$rekord['fax']."</td>";    
      echo "<td class='data_cell'>".$rekord['allapot']."</td>";
      echo "</tr>";
    }
  }
  ?>
  </table>
  <br>                        

  <?php
  // �j rendel�s �rlapja
  $form_fields = array( "N�v:"                  => "nev",
                        "Ir�ny�t�sz�m:"         => "irsz",
                        "V�ros:"                => "varos",
                        "Utca:"                 => "utca",
                        "H�zsz�m:"              => "hazszam", 
                        "E-mail:"               => "email",
                        "Telefon:"              => "telefon",
                        "Fax:"                  => "fax",                      
                        "St�tusz:"              => "allapot");

  if (isset($display_values)){
     $field_values = array( "nev"               => $nev,
                            "irsz"              => $irsz,
                            "varos"             => $varos,
                            "utca"              => $utca,
                            "hazszam"           => $hazszam,
                            "email"	          => $email,
                            "telefon"           => $telefon,
                            "fax"               => $fax,                          
                            "allapot"           => $allapot);
  }
  else{
      $field_values = array();
  }

  if (isset($_GET['id'])){
     $hidden_fields = array("id" => $_GET['id']);
  //   $form_fields['St�tusz:'] = "allapotz";
  }
  else{
      $hidden_fields = array();
  }

  if (isset($_GET['action']) && $_GET['action'] == "edit"){$headtext = "�gyf�l m�dos�t�sa";}
      else {$headtext = "�j �gyf�l felv�tele";}
  draw_form($headtext, $_SERVER['PHP_SELF'], "POST", $form_fields, $hidden_fields, $field_values, array(""));

  if ($msg != "") print '<script language="javascript">alert("'.$msg.'");</script>';  

  html_end();
} // ha van s�ti - v�ge
?>
