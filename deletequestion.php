<?php
session_start();
require('view_questions_page.php');

$page = new View_Questions_Page();

   $db = new Db();
   
   $num_results = $_GET['num_result'];
   
   
      for ($i=0; $i<$num_results; $i++)
   {
    
      $b = "delcheck".$i;
      
      if (isset($_POST[$b]))
      {$questionID = $_POST[$b];
      if ($questionID > 0) 
      {      	
          $db->deleteQuestion($questionID);
      }
      }
        
   } 

$page->Display();

?>