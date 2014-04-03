<?php

session_start();
require("member_page.php");
require("get_weeknum.php");

class Member_Home_Page extends Member_Page
{

public function Display()
{    
     echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
     
     $gooduser = $this -> authenticateUser();
     
     if ($gooduser)
     {
     $this -> DisplayMenu($this->memberbuttons);
     echo "<hr>";
     $this-> DisplayMemberHome();
     echo $this->content;
     $this->ShowPosts(5,"mhome.php");
     }
     else
     {
     $this-> DisplayMenu($this->publicbuttons);
     echo $this->content;
     if ($gooduser==0) {$this-> LogInUser();}
     }
     $this -> DisplayFooter();
     echo "</body>\n</html>\n";
}

public function DisplayMemberHome()
{
   $db = new Db();
       
   $username=$_SESSION['username'];
           
   echo "<table><tr>";
   echo "<td width=\"250\">Welcome, ".$_SESSION['username']."!</td>";
   
   $query = "select sum(totalpoints) from whoplays,competition where whoplays.username='"
      .$_SESSION['username']."' and whoplays.competitionID = competition.competitionID 
      and competition.active = '1'";
   $result = $db->query($query);
   $row = $result->FETCH_ASSOC();
   $totalpoints = $row['sum(totalpoints)'];
   
   $query = "select username, sum(totalpoints) from whoplays,competition where 
      whoplays.competitionID=competition.competitionID and 
      competition.active=1 group by username";
      
   $result = $db->query($query);
   $num_participants = $result->num_rows;
   
   $place = 1;
   for ($i=0;$i<$num_participants;$i++)
   {
       $row = $result->FETCH_ASSOC();
       if ($row['sum(totalpoints)']>$totalpoints) {$place++;}
   }
   
   if ($place==1) {$placestr='st';} elseif ($place==2) {$placestr='nd';} else {$placestr="th";}
   
   echo "<td width=\"250\">Total Points: ".$totalpoints." -  <a href=\"overallstandings.php\">".$place.$placestr." out of "
        .$num_participants."</a></td></tr>";
   
   // Current Competitions Block
   echo "<tr><td><b><br/>Your Current Competitions:</b><br/>";
   
      $query = "select * from (select * from whoplays where username='".$username."') as w right join 
   competition on competition.competitionID=w.competitionID where competition.active=1";
   
   $result = $db->query($query);
   $num_results = $result->num_rows;
   
   echo "<table border=\"2\">";
   
   for ($i=0;$i<$num_results;$i++)
   {
       $row = $result->FETCH_ASSOC();
       echo "<tr><td><a class=\"normal\" href=\"chome.php?compid=".$row['competitionID'],"\">".$row['compname']."</a></td>";
       
       if (is_null($row['totalpoints']))
       {
          echo "<td><a href=\"joincomps.php\">JOIN!</a></td></tr>";
       }
       else
       {
          echo "<td>".$row['totalpoints']." pts</td>";
       }
       
       $query2 = "select min(pick.locktime) from pick, question where question.questionID=pick.questionID and
          question.competitionID='".$row['competitionID']."' and pick.locktime>'".date("Y-m-d H:i:s",now_time()).
          "' and (pick.pick is null or pick.pick=0) and pick.username='".$_SESSION['username']."'";
        
       $result2 = $db->query($query2);
       $row2 = $result2->FETCH_ASSOC();
       $nextlock = strtotime($row2['min(pick.locktime)'])-now_time();
       
       
       if ($nextlock>0 and $nextlock<259200)
       {
           echo "<td><a href=\"makepicks.php?compID=".$row['competitionID']."\">PICK NOW!</a></td>";
       }
       elseif ($nextlock>0)
       {   
          echo "<td><a class=\"normal\" href=\"makepicks.php?compID=".$row['competitionID']."\">Make picks!</a></td>";
       }
       else
       {
         echo "<td><a class=\"normal\" href=\"makepicks.php?compID=".$row['competitionID']."\">your picks</a></td>";
       }
       echo "</tr>";
       
       
    }
    echo "</table></td>";
   
   $query = "select * from pick, question, game where pick.username = '".$_SESSION['username'].
      "' and pick.questionID = question.questionID and question.gameID = game.gameID 
      and pick.pick is null and pick.locktime>'".date("Y-m-d H:i:s",now_time())."' order by pick.locktime limit 1";
   
   $result = $db->query($query);
   $row=$result->FETCH_ASSOC();
   $nextlock = strtotime($row['locktime'])-now_time();
   
   echo "<td><b>Your next upcoming pick:</b></br>";
   
   if ($result->num_rows > 0)
   {
   
   if ($row['gameID']>0)
   {
       $query2 = "select a.location as aloc, h.location as hloc from game, team as a, team as h where gameID = '".
           $row['gameID']."' and a.teamID = game.ateamID and h.teamID = game.hteamID";
           
       $result2 = $db->query($query2);
       $row2=$result2->FETCH_ASSOC();
       
       echo $row2['aloc']." @ ".$row2['hloc']."</br>";
    }
    else
    {
       echo $row['pickname']."<br/>";
    }
    
    $days = floor($nextlock/86400);
    $hours = floor(($nextlock-86400*$days)/3600);
    $minutes = floor(($nextlock-86400*$days-3600*$hours)/60);
    $seconds = floor($nextlock-86400*$days-3600*$hours-60*$minutes);
    
    if ($days>0) {echo $days." days, ";}
    
    echo $hours." hours, ".$minutes," minutes, ".$seconds." seconds </br>";
    
   
   echo "<a href=\"makepicks.php?compID=".$row['competitionID']."\">PICK NOW!</a></td></tr>";
   }
   else
   {
      echo "No upcoming Picks</td></tr>";
   }
   
   
   echo "</table><br/><br/>";
   
}


}


?>