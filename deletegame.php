<?php
session_start();
require('view_games_page.php');

$page = new View_Game_Page();

   $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
   
   $num_results = $_GET['num_result'];
   
   
      for ($i=0; $i<$num_results; $i++)
   {
    
      $b = "delcheck".$i;
      
      if (isset($_POST[$b]))
      {$gameID = $_POST[$b];
      if ($gameID > 0) {
           $query = "delete from game where gameID='".$gameID."'";
        
           $result = $db->query($query);
           
           }
      }
        
   } 
   
$db->close();

$page->Display();

?>