<?php
include ("../includes/functions.inc.php");
include ("../includes/dirinfo.php");
include ("../includes/iam_restore.php");
$rlevel=5; //olvas�si szint
$wlevel=5; //�r�si szint -itt nem haszn�lt
$dlevel=5; //t�rl�si szint -itt nem haszn�lt

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
// A felhaszn�l�i jogok lek�rdez�se
    $user_sql = "SELECT id, szint FROM ugyintezo WHERE nev='".$_COOKIE['user_name']."'";
    
   if ($connection1->db_query($user_sql)){
       $dolgozo_id = $connection1->params['data'][0]['id'];
     $level = $connection1->params['data'][0]['szint'];
   }
    // bel�p�si szint ellen�rz�s
   if ($level<$rlevel) header("Location: ../menu/archiving.php?msg=1");

if (isset($_GET['id'])){
  // Restore megval�s�t�sa
  if (isset($_GET['action']) && $_GET['action'] == "restore"){
    $restore = new iam_restore(realpath("../biztonsagi")."//".$_GET['id'], "localhost", "iktato", "dbusername", "");
    $restore->perform_restore();
  }
}

$header_cells = array("F�jln�v", "M�ret", "L�trehoz�s ideje", "M�dos�t�s ideje");
$links = array(	"F�men�"                  => "../menu/main.php",
                "Biztons�gi ment�s"       => "../backup/backup.php",
                "Biztons�gi visszat�lt�s" => "../backup/restore.php");
// HTML fejl�c
// html_start("K�rtyagy�rt�s",$_COOKIE['user_name']." ($beosztas) - ".$header_text);
$header_text = CEGNEV." - Biztons�gi visszat�lt�s";
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
    echo "<td class='data_cell'>";$q->confirm_url( "Bet�lti a ".$dir->stat[$i]['Name']." ID-j� rekordot?",$dir->stat[$i]['Name'],$_SERVER['PHP_SELF']."?id=".$dir->stat[$i]['Name']."&action=restore");echo "</a></td>";
    echo "<td class='data_cell'>".$dir->stat[$i]['Size']."</td>";
    echo "<td class='data_cell'>".$dir->stat[$i]['Created']."</td>";
    echo "<td class='data_cell'>".$dir->stat[$i]['Modified']."</td>";
    echo "</tr>";
  } // for $id
} // if no_item > 0

echo "</table><br>";

html_end();
} // ha van s�ti - v�ge
?>