<?php

session_start();
require("update_scores_page.php");
require("db_sync.php");

$page = new Update_Scores_Page();

$page->admin_level = 1;

$page->content = "";


if (isset($_GET['numspreads']))
{
   $numspreads = $_GET['numspreads'];
   $numscores = $_GET['numscores'];
   $numother = $_GET['numother'];
   
   @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
                'fpcdata','bB()*45.ab','fpcdata');
    
    for ($i=0;$i<$numspreads;$i++)
    {
       $spread = $_POST["spread".$i];
       
       if (strlen($spread)>0)
       {
          $gameid = $_POST["spgameID".$i];
          
          $query = "update game set spread = '".$spread."' where gameID='".$gameid."'";
          
          $db->query($query);
          
       }
       
    }
    
    for ($i=0;$i<$numscores;$i++)
    {
        
        $hscore = $_POST["hscore".$i];
        $ascore = $_POST["ascore".$i];
        
        if (strlen($hscore)>0 & strlen($ascore)>0)
        {
            $gameid = $_POST["scgameID".$i];
            $query = "update game set hscore = '".$hscore."', ascore='".$ascore."' where
               gameID='".$gameid."'";
            $db->query($query);
            
            update_game($gameid,$db);
        }
    }
    
    for ($i=0; $i<$numother;$i++)
    {
    
    	$correctans = $_POST["other".$i];
    	
    	echo $correctans;
    	
    	if (strlen($correctans)>0)
    	{
    		$questionID = $_POST["otherID".$i];
    		$query = "update question set correctans='".$correctans."' 
    		where questionID = '".$questionID."'";
    		$db->query($query);
    		
    	}
    	
    }
    
}

$page->Display();




?>