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
// A felhaszn�l�i jogok lek�rdez�se
    $user_sql = "SELECT id, szint FROM ugyintezo WHERE nev='".$_COOKIE['user_name']."'";
    
   if ($connection1->db_query($user_sql)){
       $dolgozo_id = $connection1->params['data'][0]['id'];
     $level = $connection1->params['data'][0]['szint'];
   }
   
   $links = array(//"Felhaszn�l� kezel�s"     => "../user/registration.php",
		            "Program ind�t�s"  	     => "../prg/p_mainnum.php",
                    "Adatb�zisok kezel�se"   => "../menu/dbases.php",
                    "Karbantart�s"           => "../menu/archiving.php",
                    "Kil�p�s"	             => "../prg/logout.php");

   $header_text = CEGNEV." - F�men�";
   html_start(PRGNEV, $header_text);
   
   ?>

   <br>

   <?php draw_menu($links);
   if (isset($_GET['msg']) && $_GET['msg']==1){
       print '<script language="javascript">alert("�nnek nincs megfelel� jogosults�ga!");</script>';   
   } 
    
    html_end();
}
?>
