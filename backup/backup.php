<?php
include ("../includes/functions.inc.php");
include ("../includes/iam_backup.php");
$rlevel=5; //olvasási szint
$wlevel=5; //írási szint -itt nem használt
$dlevel=5; //törlési szint -itt nem használt

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

// Szerkesztés - Törlés megvalósítása
if (count($_POST) > 0){

  $errors = data_check($_POST);

  if (count($errors) == 0){
    //backup
    if (!empty($_POST["filename"])){
      $backup = new iam_backup("localhost", "iktato", "dbusername", "", true, false, true, realpath("../biztonsagi")."//".$_POST["filename"]);
      $backup->perform_backup();
    }
  }
  else{
    $msg = "HIBA!";
  }
} // UPDATE, INSERT vége

$links = array(	"Fõmenü"                   => "../menu/main.php",
                "Biztonsági mentés"        => "../backup/backup.php",
                "Biztonsági visszatöltés"  => "../backup/restore.php");

// HTML fejléc
// html_start("Kártyagyártás",$_COOKIE['user_name']." ($beosztas) - ".$header_text);
$header_text = CEGNEV." - Biztonsági mentés";
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
<?php
$headtext = "Biztonsági mentés";
draw_backupform($headtext, $_SERVER['PHP_SELF'], "POST");

html_end();
} // ha van süti - vége
?>