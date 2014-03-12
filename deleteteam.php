<?php
session_start();
require('view_team_page.php');

$page = new View_Team_Page();


   
   $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
   
   $num_results = $_GET['num_result'];
   
   for ($i=0; $i<$num_results; $i++)
   {
    
      $b = "delcheck".$i;
    
      
      if (isset($_POST[$b]))
      {$teamID = $_POST[$b];
      if ($teamID > 0) {
           $query = "delete from team where teamID='".$teamID."'";
        
           $result = $db->query($query);
           
           }
      }
        
   } 
        
$db->close();



$page->Display();

?>