<?php
include ("../includes/functions.inc.php");
include ("../includes/dirinfo.php");
include ("../includes/iam_restore.php");
$rlevel=5; //olvasási szint
$wlevel=5; //írási szint -itt nem használt
$dlevel=5; //törlési szint -itt nem használt

$q = new confirm;

$q->target='_blank';
$q->install_confirm();

$root_dir = realpath("../biztonsagi");
$root_url = "http://".$_SERVER['SERVER_NAME'];
$icon ="";
$dir = new DIRINFO($root_dir, $root_url, "*", $icon, "%d/%m/%y %H:%M");

//include("includes/jfunctions.js");
// ($_SERVER['HTTP_REFERER'] != "login.php") && 
if (empty($_COOKIE['session_id']))
{
	header("Location: ../login.php");
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
   if ($level<$rlevel) header("Location: ../menu/archiving.php?msg=1");

if (isset($_GET['id'])){
  // Restore megvalósítása
  if (isset($_GET['action']) && $_GET['action'] == "restore"){
    $restore = new iam_restore(realpath("../biztonsagi")."//".$_GET['id'], "localhost", "iktato", "dbusername", "");
    $restore->perform_restore();
  }
}

$header_cells = array("Fájlnév", "Méret", "Létrehozás ideje", "Módosítás ideje");
$links = array(	"Fõmenü"                  => "../menu/main.php",
                "Biztonsági mentés"       => "../backup/backup.php",
                "Biztonsági visszatöltés" => "../backup/restore.php");
// HTML fejléc
// html_start("Kártyagyártás",$_COOKIE['user_name']." ($beosztas) - ".$header_text);
$header_text = CEGNEV." - Biztonsági visszatöltés";
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

if ($dir->no_item > 0){
  for ($id = 0; $id < $dir->no_item; $id ++){
    $i = $dir->order[$id];
    echo "<tr>";
    echo "<td class='data_cell'>";$q->confirm_url( "Betölti a ".$dir->stat[$i]['Name']." ID-jû rekordot?",$dir->stat[$i]['Name'],$_SERVER['PHP_SELF']."?id=".$dir->stat[$i]['Name']."&action=restore");echo "</a></td>";
    echo "<td class='data_cell'>".$dir->stat[$i]['Size']."</td>";
    echo "<td class='data_cell'>".$dir->stat[$i]['Created']."</td>";
    echo "<td class='data_cell'>".$dir->stat[$i]['Modified']."</td>";
    echo "</tr>";
  } // for $id
} // if no_item > 0

echo "</table><br>";

html_end();
} // ha van süti - vége
?>