<?php
include ("../includes/functions.inc.php");
$rlevel=3; //olvas�si szint
$wlevel=3; //�r�si szint -itt nem haszn�lt
$dlevel=3; //t�rl�si szint -itt nem haszn�lt   
$msg = "";
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

      $edit_sql = "SELECT * FROM iktato WHERE id='".$_GET['id']."'";
      $connection1->db_query($edit_sql);

         if (count($connection1->params['data']) > 0){
        $id = $connection1->params['data'][0]['id'];
        $ugyintezo_id = $connection1->params['data'][0]['ugyintezo_id'];
        $ugyfel_id = $connection1->params['data'][0]['ugyfel_id'];
        $irat_id = $connection1->params['data'][0]['irat_id'];
        $foszam = $connection1->params['data'][0]['foszam'];
        $alszam = $connection1->params['data'][0]['alszam'];
        $targy = $connection1->params['data'][0]['targy'];   
        $kelt = $connection1->params['data'][0]['kelt'];   
        $melleklet = $connection1->params['data'][0]['melleklet'];
        $irany = $connection1->params['data'][0]['irany'];  
        $megjegyzes = $connection1->params['data'][0]['megjegyzes'];           
        $display_values = true;
      }
    }

  } // -- Szerkeszt�s - 

  // INSERT �S UPDATE megval�s�t�sa
  if (count($_POST) > 0){

    $errors = data_check($_POST);

    if (count($errors) == 0){
      //UPDATE
      if (!empty($_POST["id"])){
        $update_sql = "UPDATE iktato SET ugyintezo_id='".$_POST['ugyintezo_id']."', ugyfel_id='".$_POST['ugyfel_id']."', irat_id='".$_POST['irat_id']."', foszam='".$_POST['foszam']."', alszam='".$_POST['alszam']."', targy='".$_POST['targy']."', kelt='".$_POST['kelt']."', melleklet='".$_POST['melleklet']."', irany='".$_POST['irany']."', megjegyzes='".$_POST['megjegyzes']."' WHERE id='".$_POST['id']."'";
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

        $insert_sql = "INSERT INTO iktato (ugyintezo_id, ugyfel_id, irat_id, foszam, alszam, targy, kelt, melleklet, irany, megjegyzes) VALUES('".$_POST['ugyintezo_id']."','".$_POST['ugyfel_id']."','".$_POST['irat_id']."','".$_POST['foszam']."','".$_POST['alszam']."','".$_POST['targy']."','".$_POST['kelt']."','".$_POST['melleklet']."','".$_POST['irany']."','".$_POST['megjegyzes']."')";
        $connection1->db_query($insert_sql);
        if ($connection1->params['affected_rows'] == 1){
          $msg = "Besz�r�s rendben!";  
        }
        else{
          $msg = "Besz�r�si hiba! ".$insert_sql;
        }
      }
      $nh="Location: p_register.php?foszam=".$_POST['foszam'];
      header($nh);
    }
    else{
      $msg = "Hiba t�rt�n a besz�r�s/m�dos�t�s k�zben! ".$errors[0]; 
    }
  } // UPDATE, INSERT v�ge

  // Az adatok lek�rdez�se
  if (isset($_GET['foszam'])){   
    $megrendeles_sql = "SELECT iktato.id as id, ugyintezo_id, ugyfel_id, foszam, irat_id, alszam, targy, kelt, melleklet, irany, iktato.megjegyzes as igtmegjegyzes, irat.tipus as irattipus, ugyfel.nev as ugyfelnev, ugyintezo.nev as ugyintezonev FROM iktato JOIN irat ON irat.id=iktato.irat_id JOIN ugyfel ON ugyfel.id=iktato.ugyfel_id JOIN ugyintezo ON ugyintezo.id=iktato.ugyintezo_id WHERE foszam=".$_GET['foszam']." ORDER BY id ASC";
  }
  $connection1->db_query($megrendeles_sql);
  if ($connection1->params['num_rows'] > 0) $adatok = $connection1->params['data'];
  else $adatok = array();

  $header_cells = array("Id", "�gyint�z�", "�gyf�l", "Irat", "F�sz�m", "Alsz�m", "T�rgy", "Kelt", "Mell�klet", "Ir�ny", "Megjegyz�s");
  $links = array( "F�men�"       => "../menu/main.php",
                  "Vissza"       => "../prg/p_mainnum.php");

  // HTML fejl�c
  // html_start("K�rtyagy�rt�s",$_COOKIE['user_name']." ($beosztas) - ".$header_text);
  $header_text = CEGNEV." - Iktat� program";            
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
      echo "<td class='data_cell'><a href='".$_SERVER['PHP_SELF']."?id=".$rekord['id']."&foszam=".$_GET['foszam']."&action=edit'>".$rekord['id']."</a></td>";
      echo "<td class='data_cell'>".$rekord['ugyintezonev']."</td>";
      echo "<td class='data_cell'>".$rekord['ugyfelnev']."</td>";
      echo "<td class='data_cell'>".$rekord['irattipus']."</td>";
      echo "<td class='data_cell'>".$rekord['foszam']."</td>";
      echo "<td class='data_cell'>".$rekord['alszam']."</td>";
      echo "<td class='data_cell'>".$rekord['targy']."</td>"; 
      echo "<td class='data_cell'>".$rekord['kelt']."</td>";
      echo "<td class='data_cell'>".$rekord['melleklet']."</td>"; 
      echo "<td class='data_cell'>".$rekord['irany']."</td>";  
      echo "<td class='data_cell'>".$rekord['igtmegjegyzes']."</td>";            
      echo "</tr>";
    }
  }
  ?>
  </table>
  <br>
         
  <?php
  // �j rendel�s �rlapja
  $form_fields = array( "�gyint�z�:"    => "ugyintezo_id",
                        "�gyf�l:"       => "ugyfel_id",
                        "Irat:"         => "irat_id",
                        "F�sz�m:"       => "foszam",
                        "Alsz�m:"       => "alszam",
                        "T�rgy:"        => "targy",
                        "Kelt:"         => "kelt",
                        "Mell�klet:"    => "melleklet",
                        "Ir�ny:"        => "irany",
                        "Megjegyz�s:"   => "megjegyzes",);

  if (isset($display_values)){
     $field_values = array( "ugyintezo_id"    => $ugyintezo_id,
                            "ugyfel_id"       => $ugyfel_id,
                            "irat_id"         => $irat_id,
                            "foszam"          => $foszam,
                            "alszam"          => $alszam,
                            "targy"           => $targy,
                            "kelt"            => $kelt,
                            "melleklet"       => $melleklet,
                            "irany"           => $irany, 
                            "megjegyzes"      => $megjegyzes);
  }
  else{
      $field_values = array( "foszam"        => $_GET['foszam'],
                             "alszam"        => get_igtnum($_GET['foszam'])+1,
                             "kelt"          => get_nowdate());
  }

  if (isset($_GET['id'])){
     $hidden_fields = array("id" => $_GET['id']);
  //   $form_fields['St�tusz:'] = "statusz";
  }
  else{
      $hidden_fields = array();
  }

  if (isset($_GET['action']) && $_GET['action'] == "edit"){$headtext = "Iktat� m�dos�t�sa";}
    else {$headtext = "�j iktat� felv�tele";}
  draw_form($headtext, $_SERVER['PHP_SELF'], "POST", $form_fields, $hidden_fields, $field_values, array(""));

  if ($msg != "") print '<script language="javascript">alert("'.$msg.'");</script>'; //eredm�ny
  
  html_end();
} // ha van s�ti - v�ge
?>