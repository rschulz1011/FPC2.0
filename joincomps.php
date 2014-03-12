<?php

session_start();
require("join_comps_page.php");
require("get_weeknum.php");

$page = new Join_Comps_Page();

$page->content = "Join Competitions";

if (isset($_GET['joinup']))
{

    @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
       'fpcdata','bB()*45.ab','fpcdata');
    for ($i=0;$i<10;$i++)
    {
        $b = "joincheck".$i;
        $compid = $_POST[$b];
        if ($compid>0)
        {
            $query = "insert into whoplays (username,competitionID,totalpoints) values 
             ('".$_SESSION['username']."',".$compid.",0)";
            $result = $db->query($query);
            
            $query = "select questionID, locktime from question where competitionID='".$compid."' and locktime>'".
               date("Y-m-d H:i:s",now_time())."'";
            
            echo $query;
            
            $result = $db->query($query);
            $num_results = $result->num_rows;
            
            $query = "insert into pick (questionID, username, locktime) values ";
           
           for ($j=0;$j<$num_results;$j++)
           {
              $row = $result->FETCH_ASSOC();
              $query = $query."(".$row['questionID'].",'".$_SESSION['username']."','".$row['locktime']."')";
              if ($j<($num_results-1)) {$query=$query.",";}
           }
           
           $result = $db->query($query);
            
            
        }
    }
    
}

$page->Display();

?>