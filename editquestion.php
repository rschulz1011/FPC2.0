<?php

session_start();

require("edit_questions_page.php");

$page = new Edit_Questions_Page();


if (isset($_POST['pickname']))
{
     $db = new Db();
     
     $query = "update question set ";
     
     $pickname = $_POST['pickname'];
     $bonusmult = $_POST['bonusmult'];
     
     $query = $query." pickname='".$pickname."', bonusmult='".$bonusmult."'";
     
     if (isset($_POST['gameID']))
     {
          $gameID = $_POST['gameID'];
          
          $query2 = "select ateamID, hteamID, KOtime from game where gameID='".$gameID."'";
          $result2 = $db->query($query2);
          $row = $result2->FETCH_ASSOC();
          
          $option1 = $row['ateamID'];
          $option2 = $row['hteamID'];
          $locktime = $row['KOtime'];
          
          $query = $query.", gameID = '".$gameID."'";
          
          $correctans = $_POST['correctans'];
          
          
          if ($correctans>0)
          {
              if ($correctans!==$option1 and $correctans!==$option2)
              { $correctans=0; $query= $query.", correctans = null ";}
              else
              { $query = $query.", correctans='".$correctans."'";}
          }
          else
          { $correctans=0; $query= $query.", correctans = null ";}
     }
     else
     {
          $option1 = $_POST['option1'];
          $option2 = $_POST['option2'];
          $locktime = $_POST['locktime'];
          
          $correctans = $_POST['correctans'];
          
          if ($correctans>0)
          {$query = $query.", correctans='".$correctans."'";}
          else
          {$query= $query.", correctans = null ";}
     }
     
      $timeval = strtotime($locktime);
      if ($timeval==false){$error="could not parse time/date";}
     
      $query = $query.", option1='".$option1."', option2='".$option2."', locktime='".date("Y-m-d H:i:s",$timeval)."'";

      $query = $query." where questionID='".$_GET['questionID']."'";
      
      $result = $db->query($query);
      $changed = $db->getAffectedRows();
            
      if ($changed) 
      {$page->content="<g class=\"good\">Question Updated Sucessfully</g><br/>";}
      else
      {$error = "Unable to update question: Database Error";}
      
     if (strlen($error)>0)
    {$page->content="<g class=\"bad\">".$error."</g><br/>";}

}


$page->Display();

?>


