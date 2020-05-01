<?php
include ("../includes/functions.inc.php");
$rlevel=3; //olvasási szint
$wlevel=3; //írási szint  -itt nem használt   
$dlevel=3; //törlési szint -itt nem használt   
$msg = "";
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

  // INSERT megvalósítása
  if (count($_POST) > 0){

    $errors = data_check($_POST);

    if (count($errors) == 0){
      //INSERT
      if (empty($_POST["id"])){
   
        $insert_sql = "INSERT INTO iktato (ugyintezo_id, ugyfel_id, irat_id, foszam, alszam, targy, kelt, melleklet, irany, megjegyzes) VALUES('".$_POST['ugyintezo_id']."','".$_POST['ugyfel_id']."','".$_POST['irat_id']."','".$_POST['foszam']."','".$_POST['alszam']."','".$_POST['targy']."','".$_POST['kelt']."','".$_POST['melleklet']."','".$_POST['irany']."','".$_POST['megjegyzes']."')";
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
  $megrendeles_sql = "SELECT * FROM iktato GROUP BY foszam";
  $connection1->db_query($megrendeles_sql);
  if ($connection1->params['num_rows'] > 0) $adatok = $connection1->params['data'];
  else $adatok = array();

  $header_cells = array("Id", "Fõszám", "Alszám", "Tárgy", "Kelt", "Funkció");
  $links = array( "Fõmenü"               => "../menu/main.php",
                  "Fõszám iktatás"       => "../prg/p_mainnum.php",
                  "Ügyfél kezelés"       => "../prg/p_contact.php");

  // HTML fejléc
  // html_start("Kártyagyártás",$_COOKIE['user_name']." ($beosztas) - ".$header_text);
  $header_text = CEGNEV." - Iktató program";
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
      echo "<td class='data_cell'>".$rekord['id']."</td>";
      echo "<td class='data_cell'>".$rekord['foszam']."</td>";
      echo "<td class='data_cell'>".$rekord['alszam']."</td>";
      echo "<td class='data_cell'>".$rekord['targy']."</td>"; 
      echo "<td class='data_cell'>".$rekord['kelt']."</td>";
      echo "<td class='data_cell'><a href=../prg/p_register.php?foszam=".$rekord['foszam'].">Lista</a></td>";
      echo "</tr>";
    }
  }
  ?>
  </table>
  <br>
         
  <?php
  // Új rendelés ûrlapja
  $form_fields = array( "Ügyintézõ:"    => "ugyintezo_id",
                        "Ügyfél:"       => "ugyfel_id",
                        "Irat:"         => "irat_id",
                        "Fõszám:"       => "foszam",
                        "Alszám:"       => "alszam",
                        "Tárgy:"        => "targy",
                        "Kelt:"         => "kelt",
                        "Melléklet:"    => "melleklet",
                        "Irány:"        => "irany",
                        "Megjegyzés:"   => "megjegyzes",);
   $i = get_mainnum();
   $field_values = array(  "foszam"        => $i+1,
                           "alszam"        => 1,
                           "kelt"          => get_nowdate());

  if (isset($_GET['id'])){
     $hidden_fields = array("id" => $_GET['id']);
  //   $form_fields['Státusz:'] = "statusz";
  }
  else{
      $hidden_fields = array();
  }

  if (isset($_GET['action']) && $_GET['action'] == "edit"){$headtext = "Iktató módosítása";}
    else {$headtext = "Új iktató felvétele";}
  draw_form($headtext, $_SERVER['PHP_SELF'], "POST", $form_fields, $hidden_fields, $field_values, array(""));

  if ($msg != "") print '<script language="javascript">alert("'.$msg.'");</script>';  //eredmény  
  
  html_end();
} // ha van süti - vége
?>