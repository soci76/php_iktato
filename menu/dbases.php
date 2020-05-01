<?php
include("../includes/functions.inc.php");

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
   $links = $simplelinks;

   $header_text = CEGNEV." - Adatbázisok kezelése";
   html_start(PRGNEV, $header_text);

   ?>

   <br>

   <?php draw_menu($links);
   if (isset($_GET['msg']) && $_GET['msg']==1){
       print '<script language="javascript">alert("Önnek nincs megfelelõ jogosultsága!");</script>';   
   } 
   html_end();
}
?>