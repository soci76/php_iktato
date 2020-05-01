<?php
include ("../includes/functions.inc.php");
// szintek beállítása
$rlevel=3; //olvasási szint
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

      $edit_sql = "SELECT * FROM irat WHERE id='".$_GET['id']."'";
      $connection1->db_query($edit_sql);

      if (count($connection1->params['data']) > 0){
        $id = $connection1->params['data'][0]['id'];
        $tipus = $connection1->params['data'][0]['tipus'];
        $megjegyzes = $connection1->params['data'][0]['megjegyzes']; 
        $display_values = true;
      }
    }
    // Törlés megvalósítása
    if (isset($_GET['action']) && $_GET['action'] == "delete"){

      $delete_sql = "DELETE FROM irat WHERE id='".$_GET['id']."'";
      $connection1->db_query($delete_sql);
      header("Location: docu.php");

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

        $update_sql = "UPDATE irat SET tipus='".$_POST['tipus']."', megjegyzes='".$_POST['megjegyzes']."' WHERE id='".$_POST['id']."'";
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

        $insert_sql = "INSERT INTO irat (tipus,megjegyzes) VALUES('".$_POST['tipus']."','".$_POST['megjegyzes']."')";
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
      $msg = "Hiba történ a beszúrás/módosítás közben!";
    }
  } // UPDATE, INSERT vége

  // Az adatok lekérdezése
  $megrendeles_sql = "SELECT * FROM irat";
  $connection1->db_query($megrendeles_sql);
  if ($connection1->params['num_rows'] > 0) $adatok = $connection1->params['data'];
  else $adatok = array();

  $header_cells = array("Id", "Tipus", "Megjegyzés", "Funkció");
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
      echo "<td class='data_cell'>".$rekord['tipus']."</td>";
      echo "<td class='data_cell'>".$rekord['megjegyzes']."</td>";
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
  $form_fields = array( "Típus:"       => "tipus",
                        "Megjegyzés:"  => "megjegyzes");
                            
  if (isset($display_values)){
     $field_values = array( "tipus"        => $tipus,
                            "megjegyzes"   => $megjegyzes);   
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
    if (isset($_GET['action']) && $_GET['action'] == "edit"){$headtext = "Irattípus módosítása";}
      else {$headtext = "Új irattípus felvétele";}
    draw_form($headtext, $_SERVER['PHP_SELF'], "POST", $form_fields, $hidden_fields, $field_values, array(""));
  }
  if ($msg != "") print '<script language="javascript">alert("'.$msg.'");</script>';  
  
  html_end();
} // ha van süti - vége
?>
