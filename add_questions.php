<?php

   session_start();
   require("add_questions_page.php");
   
   $page = new Add_Questions_Page();
   
   @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
   
   for ($i=0;$i<16;$i++)
   {
       $b = "picktype".$i;
       if ($_POST[$b]>0)
       {
           if($_POST[$b]==1){$picktype="ATS";}
           elseif($_POST[$b]==2){$picktype="ATS-C";}
           elseif($_POST[$b]==3){$picktype="S-COL";}
           elseif($_POST[$b]==4){$picktype="S-PRO";}
           elseif($_POST[$b]==5){$picktype="OTHER";}
           
           $b = "game".$i;
           $game = $_POST[$b];
           
           $b = "name".$i;
           $name = $_POST[$b];
           
           $b = "optA".$i;
           $optA = $_POST[$b];
           
           $b = "optB".$i;
           $optB = $_POST[$b];
           
           $b = "bonus".$i;
           $bonus = $_POST[$b];
           
           $b = "locktime".$i;
           $locktime = $_POST[$b];
           $timeval = strtotime($locktime);
           $locktime = date("Y-m-d H:i:s",$timeval);
           
           if ($picktype=="ATS" or $picktype=="ATS-C")
           {
               $query = "select ateamID,hteamID,KOtime from game where gameID='".$game."'";
               $result = $db->query($query);
               $row = $result->fetch_assoc();
               
               $optA = $row['ateamID'];
               $optB = $row['hteamID'];
               $locktime = $row['KOtime'];
               
           }
           elseif ($picktype=="S-COL" or $picktype=="S-PRO")
           {
               $optA = "";
               $optB = "";
               
               $query = "select league from competition where competitionID = ".$_GET['compID'];
               $result = $db->query($query);
               $row = $result->fetch_assoc();
               
               $league = $row['league'];
               
               $query = "select max(game.KOtime) from game, team where team.teamID=game.hteamID 
               and team.league='".$league."' and game.weeknum='".$_GET['weeknum']."'";
               
               echo $query;
               
               $result = $db->query($query);
               $row = $result->fetch_array();
               
               $locktime = $row[0];
               
               
               
           }
           
           if ($bonus<1) {$bonus=1;}
           
           $query = "insert into question (competitionID,gameID,pickname,picktype,weeknum,option1,
                  option2,bonusmult,locktime) values (".$_GET['compID'].",".$game.",'".$name."','".
                  $picktype."',".$_GET['weeknum'].",'".$optA."','".$optB."',".$bonus.",'".$locktime."')";
                         
           $result = $db->query($query);
           
           $query = "select max(questionID) from question";
           $result = $db->query($query);
           $row = $result->FETCH_ASSOC();
           $questionID = $row['max(questionID)'];
           
           $query = "select username from whoplays where competitionID = '".$_GET['compID']."'";
           $result = $db->query($query);
           $num_results = $result->num_rows;
           
           $query = "insert into pick (questionID, username, locktime) values ";
           
           for ($j=0;$j<$num_results;$j++)
           {
              $row = $result->FETCH_ASSOC();
              $query = $query."(".$questionID.",'".$row['username']."','".$locktime."')";
              if ($j<($num_results-1)) {$query=$query.",";}
           }
           
           $result = $db->query($query);
           
           
        }
   }
   
   
   $db->close();
   $page->Display();
   
   
?>