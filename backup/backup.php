<?php
include ("../includes/functions.inc.php");
include ("../includes/iam_backup.php");
$rlevel=5; //olvas�si szint
$wlevel=5; //�r�si szint -itt nem haszn�lt
$dlevel=5; //t�rl�si szint -itt nem haszn�lt

//include("includes/jfunctions.js");
// ($_SERVER['HTTP_REFERER'] != "login.php") && 
if (empty($_COOKIE['session_id']))
{
	header("Location: ../login.php");
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
   if ($level<$rlevel) header("Location: ../menu/archiving.php?msg=1");

// Szerkeszt�s - T�rl�s megval�s�t�sa
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
} // UPDATE, INSERT v�ge

$links = array(	"F�men�"                   => "../menu/main.php",
                "Biztons�gi ment�s"        => "../backup/backup.php",
                "Biztons�gi visszat�lt�s"  => "../backup/restore.php");

// HTML fejl�c
// html_start("K�rtyagy�rt�s",$_COOKIE['user_name']." ($beosztas) - ".$header_text);
$header_text = CEGNEV." - Biztons�gi ment�s";
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
$headtext = "Biztons�gi ment�s";
draw_backupform($headtext, $_SERVER['PHP_SELF'], "POST");

html_end();
} // ha van s�ti - v�ge
?>