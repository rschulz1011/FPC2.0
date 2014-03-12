<?php
session_start();
require('view_questions_page.php');

$page = new View_Questions_Page();

   $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
   
   $num_results = $_GET['num_result'];
   
   
      for ($i=0; $i<$num_results; $i++)
   {
    
      $b = "delcheck".$i;
      
      if (isset($_POST[$b]))
      {$questionID = $_POST[$b];
      if ($questionID > 0) {
           $query = "delete from question where questionID='".$questionID."'";
        
           $result = $db->query($query);
           
           $query = "delete from pick where questionID='".$questionID."'";
           
           $result = $db->query($query);
           
           
           }
      }
        
   } 
   
$db->close();

$page->Display();

?>