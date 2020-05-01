<?php
include ("../includes/functions.inc.php");
$rlevel=4; //olvasási szint
$wlevel=4; //írási szint
$dlevel=4; //törlési szint

// ($_SERVER['HTTP_REFERER'] != "login.php") && 
if (empty($_COOKIE['session_id'])){
	header("Location: ../prg/login.php");
}
else{
// A felhasználói jogok lekérdezése
   $user_sql = "SELECT id, szint FROM ugyintezo WHERE nev='".$_COOKIE['user_name']."'";
    
   if ($connection1->db_query($user_sql)){
       $dolgozo_id = $connection1->params['data'][0]['id'];
     $level = $connection1->params['data'][0]['szint'];
   }
    // belépési szint ellenõrzés
   if ($level<$rlevel) header("Location: ../menu/dbases.php?msg=1");

  $q = new confirm;
  $q->target='_blank';
  $q->install_confirm();
  $msg = "";

  // Szerkesztés - Törlés megvalósítása
  if (isset($_GET['id'])){
    // Szerkesztés megvalósítása
    if (isset($_GET['action']) && $_GET['action'] == "edit"){

      $edit_sql = "SELECT * FROM ugyintezo WHERE id='".$_GET['id']."'";
      $connection1->db_query($edit_sql);

      if (count($connection1->params['data']) > 0){
        $id = $connection1->params['data'][0]['id'];
        $nev = $connection1->params['data'][0]['nev'];
        $jelszo = $connection1->params['data'][0]['jelszo'];
        $beosztas = $connection1->params['data'][0]['beosztas'];
        $teljes_nev = $connection1->params['data'][0]['teljes_nev'];
        $szint = $connection1->params['data'][0]['szint'];
        $kod = $connection1->params['data'][0]['kod'];
        $display_values = true;
      }
    }
    // Törlés megvalósítása
    if (isset($_GET['action']) && $_GET['action'] == "delete"){

      $delete_sql = "DELETE FROM ugyintezo WHERE id='".$_GET['id']."'";
      $connection1->db_query($delete_sql);
      header("Location: admin.php");

      if ($connection1->params['affected_rows'] == 1){
        $msg = "Törlés rendben!";
      }
      else{
        $msg = "Törlési hiba! ".$delete_sql;
      }
    }
  } // -- Szerkesztés - Törlés vége

  // INSERT ÉS UPDATE megvalósítása
  if (count($_POST) > 0){

    $errors = data_check($_POST);

    if (count($errors) == 0){
      //UPDATE
      if (!empty($_POST["id"])){
        $update_sql = "UPDATE ugyintezo SET nev='".$_POST['nev']."', jelszo='".$_POST['jelszo']."', beosztas='".$_POST['beosztas']."', teljes_nev='".$_POST['teljes_nev']."', szint='".$_POST['szint']."', kod='".$_POST['kod']."' WHERE id='".$_POST['id']."'";
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

        $insert_sql = "INSERT INTO ugyintezo (nev, jelszo, beosztas, teljes_nev, szint, kod) VALUES('".$_POST['nev']."','".$_POST['jelszo']."','".$_POST['beosztas']."','".$_POST['teljes_nev']."','".$_POST['szint']."','".$_POST['kod']."')";
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
  $megrendeles_sql = "SELECT * FROM ugyintezo";
  $connection1->db_query($megrendeles_sql);
  if ($connection1->params['num_rows'] > 0) $adatok = $connection1->params['data'];
  else $adatok = array();

  $header_cells = array("Id", "Név", "Jelszó", "Beosztás", "Teljes név", 'Szint', "Kód", "Funkció");
  $links = $simplelinks;

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
      //echo "<td class='data_cell'>".$rekord['jelszo']."</td>";
      echo "<td class='data_cell'>******</td>";
      echo "<td class='data_cell'>".$rekord['beosztas']."</td>";
      echo "<td class='data_cell'>".$rekord['teljes_nev']."</td>";
      echo "<td class='data_cell'>".$rekord['szint']."</td>";
      echo "<td class='data_cell'>".$rekord['kod']."</td>";      
      if ($level>=$dlevel) {echo "<td class='data_cell'>";$q->confirm_url( "Törli a ".$rekord['id']." ID-jû rekordot?",'Törlés',$_SERVER['PHP_SELF']."?id=".$rekord['id']."&action=delete");echo "</a></td>";}
      else echo "<td class='data_cell'></td>"; 
      echo "</tr>";
    }
  }
  ?>
  </table>
  <br>

  <?php
  // Új rendelés ûrlapja
  $form_fields = array( "Név:"         => "nev",
  					    "Jelszó:"  	   => "jelszo",
  					    "Beosztás"     => "beosztas",
  					    "Teljes név:"  => "teljes_nev",
  					    "Szint"  	   => "szint",
  		        		"Kód:"         => "kod");

  if (isset($display_values)){
     $field_values = array( "nev"		    => $nev,
     					    "jelszo"	    => $jelszo,
     					    "beosztas"	    => $beosztas,
     					    "teljes_nev"	=> $teljes_nev,
     					    "szint"		    => $szint,
                            "kod"		    => $kod);
  }
  else{
      $field_values = array();
  }

  if (isset($_GET['id'])){
     $hidden_fields = array("id" => $_GET['id']);
  //   $form_fields['Státusz:'] = "statusz";
  }
  else{
      $hidden_fields = array();
  }
  if ($level>=$wlevel){
    if (isset($_GET['action']) && $_GET['action'] == "edit"){$headtext = "Ügyintézõ módosítása";}
      else {$headtext = "Új ügyintézõ felvétele";}
    draw_form($headtext, $_SERVER['PHP_SELF'], "POST", $form_fields, $hidden_fields, $field_values, array(""));
  }
  if ($msg != "") print '<script language="javascript">alert("'.$msg.'");</script>'; 
  
  html_end();
} // ha van süti - vége
?>
