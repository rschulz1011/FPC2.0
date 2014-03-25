<?php
session_start();
require('view_team_page.php');

$page = new View_Team_Page();


   
   $db = new Db();
   
   $num_results = $_GET['num_result'];
   
   for ($i=0; $i<$num_results; $i++)
   {
    
      $b = "delcheck".$i;
    
      
      if (isset($_POST[$b]))
      {
      	$teamID = $_POST[$b];
      	if ($teamID > 0) 
      	{
      		$db->deleteTeam($teamID);
        }
      }
        
   } 
$page->Display();
?>