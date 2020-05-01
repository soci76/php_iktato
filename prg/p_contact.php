<?php
include ("../includes/functions.inc.php");
$rlevel=3; //olvasási szint
$wlevel=3; //írási szint -itt nem használt   
$dlevel=3; //törlési szint  -itt nem használt   
$msg = ""; 

//include("includes/jfunctions.js");
// ($_SERVER['HTTP_REFERER'] != "login.php") && 
if (empty($_COOKIE['session_id']))
{
	header("Location: ../prg/login.php");
}
else
{
// A felhasználói jogok lekérdezése
    $user_sql = "SELECT id, szint FROM ugyintezo WHERE nev='".$_COOKIE['user_name']."'";
    
   if ($connection1->db_query($user_sql)){
       $dolgozo_id = $connection1->params['data'][0]['id'];
     $level = $connection1->params['data'][0]['szint'];
   }
    // belépési szint ellenõrzés
   if ($level<$rlevel) header("Location: ../menu/main.php?msg=1");

  // Szerkesztés megvalósítása
  if (isset($_GET['id'])){
    // Szerkesztés megvalósítása
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
  } // -- Szerkesztés vége

  // INSERT ÉS UPDATE megvalósítása                                                                                                                                 
  if (count($_POST) > 0){

    $errors = data_check($_POST);

    if (count($errors) == 0){
      //UPDATE                                                                                                      
      if (!empty($_POST["id"])){
        $update_sql = "UPDATE ugyfel SET nev='".$_POST['nev']."', irsz='".$_POST['irsz']."', varos='".$_POST['varos']."', utca='".$_POST['utca']."', hazszam='".$_POST['hazszam']."', email='".$_POST['email']."', telefon='".$_POST['telefon']."', fax='".$_POST['fax']."', allapot='".$_POST['allapot']."' WHERE id='".$_POST['id']."'";
        $connection1->db_query($update_sql);
        if ($connection1->params['affected_rows'] == 1){
          $msg = "Módosítás rendben!";
        }
        else{
          $msg = "Módositási hiba! ".$update_sql;
        }
      }
      //INSERT
      else{                                                                                                                                           
        $insert_sql = "INSERT INTO ugyfel (nev,irsz,varos,utca,hazszam,email,telefon,fax,allapot) VALUES('".$_POST['nev']."','".$_POST['irsz']."','".$_POST['varos']."','".$_POST['utca']."','".$_POST['hazszam']."','".$_POST['email']."','".$_POST['telefon']."','".$_POST['fax']."','".$_POST['allapot']."')";
        $connection1->db_query($insert_sql);
        if ($connection1->params['affected_rows'] == 1){
          $msg = "Beszúrás rendben!";
        }
        else{
          $msg = "Beszúrási hiba! ".$insert_sql;
        }
      }
    }
    else{
      $msg = "Hiba történ a beszúrás/módosítás közben! ".$errors[0];
    }
  } // UPDATE, INSERT vége

  // Az adatok lekérdezése
  $megrendeles_sql = "SELECT * FROM ugyfel";
  $connection1->db_query($megrendeles_sql);
  if ($connection1->params['num_rows'] > 0) $adatok = $connection1->params['data'];
  else $adatok = array();                           

  $header_cells = array("Id", "Név", "Irányítószám", "Város", "Utca", "Házszám", "E-mail", "Telefon", "Fax","Státusz");
  $links = array( "Fõmenü"               => "../menu/main.php",
                  "Fõszám iktatás"       => "../prg/p_mainnum.php",
                  "Ügyfél kezelés"       => "../prg/p_contact.php");

  // HTML fejléc
  // html_start("Kártyagyártás",$_COOKIE['user_name']." ($beosztas) - ".$header_text);
  $header_text = CEGNEV." - Egyszerû adatbázisok";
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
  // Új rendelés ûrlapja
  $form_fields = array( "Név:"                  => "nev",
                        "Irányítószám:"         => "irsz",
                        "Város:"                => "varos",
                        "Utca:"                 => "utca",
                        "Házszám:"              => "hazszam", 
                        "E-mail:"               => "email",
                        "Telefon:"              => "telefon",
                        "Fax:"                  => "fax",                      
                        "Státusz:"              => "allapot");

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
  //   $form_fields['Státusz:'] = "allapotz";
  }
  else{
      $hidden_fields = array();
  }

  if (isset($_GET['action']) && $_GET['action'] == "edit"){$headtext = "Ügyfél módosítása";}
      else {$headtext = "Új ügyfél felvétele";}
  draw_form($headtext, $_SERVER['PHP_SELF'], "POST", $form_fields, $hidden_fields, $field_values, array(""));

  if ($msg != "") print '<script language="javascript">alert("'.$msg.'");</script>';  

  html_end();
} // ha van süti - vége
?>
