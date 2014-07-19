<?php

session_start();
require("member_page.php");
require("get_weeknum.php");

class Member_Home_Page extends Member_Page
{

public function Display()
{    
	$gooduser = $this -> authenticateUser();
	 echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
     echo "<html>\n<head>\n";
     $this -> AddScripts();
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
     
     if ($gooduser)
     {
     $this -> DisplayMenu($this->memberbuttons);
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

private function AddScripts()
{
	echo "<script src=\"http://code.jquery.com/jquery-latest.min.js\"
        type=\"text/javascript\"></script>";
	echo "<script src=\"js/comp-home.js\" type=\"text/javascript\"></script>";
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
        .$num_participants."</a></td></tr></table>";
   
   
   
   // Current Competitions Block
   echo "<div id=\"current-competitions\"><p class=\"comps-text\">Your Current Competitions:</p>";
   
      $query = "select * from (select * from whoplays where username='".$username."') as w right join 
   competition on competition.competitionID=w.competitionID where competition.active=1";
   
   $result = $db->query($query);
   $num_results = $result->num_rows;
   
   for ($i=0;$i<$num_results;$i++)
   {
       $row = $result->FETCH_ASSOC();
       echo "<a class=\"comp-link\" href=\"chome.php?compid=".$row['competitionID'],"\">"
       		."<img src=\"images/comp-".$row['defaultpick']."\" ></img>";
       				
       echo "<p class=\"comp-title\">".$row['compname']."</p>";
       
       if (is_null($row['totalpoints']))
       {
           $db->joinCompetition($_SESSION['username'],$row['competitionID']);
          echo "<span class=\"totalpoints\">0 pts</span>";
       }
       else
       {
          echo "<span class=\"totalpoints\">".$row['totalpoints']." pts</span>";
       }
       
       $query2 = "select pick.locktime from pick, question where question.questionID=pick.questionID and
          question.competitionID='".$row['competitionID']."' and pick.locktime>'".date("Y-m-d H:i:s",now_time()).
          "' and (pick.pick is null or pick.pick=0) and pick.username='".$_SESSION['username']."' order by pick.locktime limit 1";
       $result2 = $db->query($query2);
       $row2 = $result2->FETCH_ASSOC();
       $nextlock = strtotime($row2['locktime'])-now_time();
       if ($nextlock>0 and $nextlock<259200)
       {
           echo "<span class=\"makepicks picknow\" onclick=\"makepicks.php?compID=".$row['competitionID']."\">PICK NOW!</span>";
       }
       elseif ($nextlock>0)
       {   
          echo "<span class=\"makepicks picknow\" onclick=\"makepicks.php?compID=".$row['competitionID']."\">Make picks!</span>";
       }
       else
       {
         echo "<span class=\"makepicks normal\" onclick=\"makepicks.php?compID=".$row['competitionID']."\">your picks</span>";
       }
       echo "</a>";
       
       
    }
    echo "</div>";
   
   $query = "select * from pick, question, game where pick.username = '".$_SESSION['username'].
      "' and pick.questionID = question.questionID and question.gameID = game.gameID 
      and (pick.pick is null or pick.pick=0) and pick.locktime>'".date("Y-m-d H:i:s",now_time())."' order by pick.locktime limit 1";
   $result = $db->query($query);
   $row=$result->FETCH_ASSOC();
   $nextlock = strtotime($row['locktime'])-now_time();
   
   echo "<div id=\"memberHomeLinks\">";
   echo "<div id=\"nextPick\">";
   echo "<p class=\"nextPickTitle\">Your next upcoming pick:</p>";
   
   if ($result->num_rows > 0)
   {
   echo "<p class=\"nextPickLine\">";
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
    
   
   echo "<a href=\"makepicks.php?compID=".$row['competitionID']."\">PICK NOW!</a>";
   }
   else
   {
      echo "<p class=\"nextPickLine\">No upcoming Picks</p></div>";
   }
   
   if ($_SESSION['adminlev']>0)
   { echo "<a href=\"adminhome.php\">Admin Home</a><br/>";}
   
   echo "<a href=\"editprofile.php\">Edit Your Profile</a><br/>";
   
   echo "</div>";
   
   echo "</table><br/><br/>";
   
}


}


?>