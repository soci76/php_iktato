<?php
include ("../includes/functions.inc.php");
$rlevel=4; //olvas�si szint
$wlevel=4; //�r�si szint
$dlevel=4; //t�rl�si szint

// ($_SERVER['HTTP_REFERER'] != "login.php") && 
if (empty($_COOKIE['session_id'])){
	header("Location: ../prg/login.php");
}
else{
// A felhaszn�l�i jogok lek�rdez�se
   $user_sql = "SELECT id, szint FROM ugyintezo WHERE nev='".$_COOKIE['user_name']."'";
    
   if ($connection1->db_query($user_sql)){
       $dolgozo_id = $connection1->params['data'][0]['id'];
     $level = $connection1->params['data'][0]['szint'];
   }
    // bel�p�si szint ellen�rz�s
   if ($level<$rlevel) header("Location: ../menu/dbases.php?msg=1");

  $q = new confirm;
  $q->target='_blank';
  $q->install_confirm();
  $msg = "";

  // Szerkeszt�s - T�rl�s megval�s�t�sa
  if (isset($_GET['id'])){
    // Szerkeszt�s megval�s�t�sa
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
    // T�rl�s megval�s�t�sa
    if (isset($_GET['action']) && $_GET['action'] == "delete"){

      $delete_sql = "DELETE FROM ugyintezo WHERE id='".$_GET['id']."'";
      $connection1->db_query($delete_sql);
      header("Location: admin.php");

      if ($connection1->params['affected_rows'] == 1){
        $msg = "T�rl�s rendben!";
      }
      else{
        $msg = "T�rl�si hiba! ".$delete_sql;
      }
    }
  } // -- Szerkeszt�s - T�rl�s v�ge

  // INSERT �S UPDATE megval�s�t�sa
  if (count($_POST) > 0){

    $errors = data_check($_POST);

    if (count($errors) == 0){
      //UPDATE
      if (!empty($_POST["id"])){
        $update_sql = "UPDATE ugyintezo SET nev='".$_POST['nev']."', jelszo='".$_POST['jelszo']."', beosztas='".$_POST['beosztas']."', teljes_nev='".$_POST['teljes_nev']."', szint='".$_POST['szint']."', kod='".$_POST['kod']."' WHERE id='".$_POST['id']."'";
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

        $insert_sql = "INSERT INTO ugyintezo (nev, jelszo, beosztas, teljes_nev, szint, kod) VALUES('".$_POST['nev']."','".$_POST['jelszo']."','".$_POST['beosztas']."','".$_POST['teljes_nev']."','".$_POST['szint']."','".$_POST['kod']."')";
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
  $megrendeles_sql = "SELECT * FROM ugyintezo";
  $connection1->db_query($megrendeles_sql);
  if ($connection1->params['num_rows'] > 0) $adatok = $connection1->params['data'];
  else $adatok = array();

  $header_cells = array("Id", "N�v", "Jelsz�", "Beoszt�s", "Teljes n�v", 'Szint', "K�d", "Funkci�");
  $links = $simplelinks;

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
      //echo "<td class='data_cell'>".$rekord['jelszo']."</td>";
      echo "<td class='data_cell'>******</td>";
      echo "<td class='data_cell'>".$rekord['beosztas']."</td>";
      echo "<td class='data_cell'>".$rekord['teljes_nev']."</td>";
      echo "<td class='data_cell'>".$rekord['szint']."</td>";
      echo "<td class='data_cell'>".$rekord['kod']."</td>";      
      if ($level>=$dlevel) {echo "<td class='data_cell'>";$q->confirm_url( "T�rli a ".$rekord['id']." ID-j� rekordot?",'T�rl�s',$_SERVER['PHP_SELF']."?id=".$rekord['id']."&action=delete");echo "</a></td>";}
      else echo "<td class='data_cell'></td>"; 
      echo "</tr>";
    }
  }
  ?>
  </table>
  <br>

  <?php
  // �j rendel�s �rlapja
  $form_fields = array( "N�v:"         => "nev",
  					    "Jelsz�:"  	   => "jelszo",
  					    "Beoszt�s"     => "beosztas",
  					    "Teljes n�v:"  => "teljes_nev",
  					    "Szint"  	   => "szint",
  		        		"K�d:"         => "kod");

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
  //   $form_fields['St�tusz:'] = "statusz";
  }
  else{
      $hidden_fields = array();
  }
  if ($level>=$wlevel){
    if (isset($_GET['action']) && $_GET['action'] == "edit"){$headtext = "�gyint�z� m�dos�t�sa";}
      else {$headtext = "�j �gyint�z� felv�tele";}
    draw_form($headtext, $_SERVER['PHP_SELF'], "POST", $form_fields, $hidden_fields, $field_values, array(""));
  }
  if ($msg != "") print '<script language="javascript">alert("'.$msg.'");</script>'; 
  
  html_end();
} // ha van s�ti - v�ge
?>
