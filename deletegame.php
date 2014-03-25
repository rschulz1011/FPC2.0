<?php
session_start();
require('view_games_page.php');

$page = new View_Game_Page();

   $db = new Db();
   
   $num_results = $_GET['num_result'];
   
   for ($i=0; $i<$num_results; $i++)
   {
    
      $b = "delcheck".$i;
      
      if (isset($_POST[$b]))
      {
      	$gameID = $_POST[$b];
      	if ($gameID > 0) 
      	{
   			$db->deleteGame($gameID);
        }
      }
        
   } 

$page->Display();

?>