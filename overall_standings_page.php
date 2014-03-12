<?php

session_start();
require("page.php");

class Overall_Standings_Page extends page

{

  public function Display()
  {
     echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
     $this -> authenticateUser();
     $this -> DisplayMenu($this->buttons);
     echo "<hr>";
     echo $this->content;
     echo $this->DisplayStandings();
     $this -> DisplayFooter();
     echo "</body>\n</html>\n";
  }
  
  public function DisplayStandings()
  {
       @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
      'fpcdata','bB()*45.ab','fpcdata');
      
      $query = "select * from competition where active=1";
      
      $result = $db->query($query);
      $num_comps = $result->num_rows;
      
      $selectstr = "";
      $fromstr = "";
      $wherestr = "";
      
      for ($i=0;$i<$num_comps;$i++)
      {
         $row = $result->fetch_assoc();
         $selectstr = $selectstr.", a".$i.".totalpoints as tp".$i;
         
         $fromstr = $fromstr.",(select totalpoints,username from whoplays where competitionID='".
               $row['competitionID']."') as a".$i;
               
         if ($i>0) {$wherestr=$wherestr." and ";}
         
         $wherestr = $wherestr." a".$i.".username=whoplays.username";
         
      }
      
      $query = "select whoplays.username, 
                tp.totalpoints ".$selectstr." from whoplays,
                (select sum(whoplays.totalpoints) as totalpoints, username from whoplays, competition 
                where competition.competitionID=whoplays.competitionID and competition.active=1 group by username) as tp ".
           $fromstr." where ".$wherestr." and tp.username=whoplays.username group by whoplays.username order by tp.totalpoints desc";
      
      $sresult = $db->query($query);
      $numusers = $sresult->num_rows;
    
    
      $ptsmat = array($numusers);
      $rankmat = array($numusers);
      
      for ($x=0;$x<$numusers;$x++)
      {
         $ptsmat[$x] = array($num_comps);
         $rankmat[$x] = array($num_comps);
      }      
      
      for($i=0;$i<$numusers;$i++)
      {
         $row = $sresult->fetch_assoc();
         
         for ($j=0;$j<$num_comps;$j++)
         {
            $ptsmat[$i][$j] = $row["tp".$j];
            
         }
         
      }
      mysqli_data_seek($sresult,0);
      
      for($i=0;$i<$numusers;$i++)
      {
         $row = $result->fetch_assoc();
         
         for ($j=0;$j<$num_comps;$j++)
         {
             $temprank = 1;
             for ($k=0;$k<$numusers;$k++)
             {
                 if ($ptsmat[$k][$j] > $ptsmat[$i][$j]) {$temprank++;}
             }
             
             $rankmat[$i][$j] = $temprank;
             
         }
         
      }
      
    
      echo "<h3> Overall Standings </h3>";
    
      echo "<table cellpadding=\"5\"><tr><th>Rank</th><th> User</th><th> Total Points </th>";
      
      mysqli_data_seek($result,0);
      
      for ($i=0;$i<$num_comps;$i++)
      {
         $row = $result->fetch_assoc();
         echo "<th>  ".$row['compname']."  </th>";
      }
      
      echo "</tr>";
      
      $lastpts = -999;
      $rank = 0;
      for ($i=0;$i<$numusers;$i++)
      {
         $row = $sresult->fetch_assoc();
         
         if ($row['totalpoints']!=$lastpts) {$rank=$i+1;} 
         $lastpts = $row['totalpoints'];
         
         echo "<tr ";
         if ($_SESSION['username']==$row['username']) {echo " class=\"highlight\" ";}
         elseif (($i % 2)==1) {echo " class=\"shaded\" ";}
         echo " ><td align=\"center\">".$rank."</td><td>".$row['username']."</td><td align=\"center\"><b>".$row['totalpoints']."</td>";
         
         for ($j=0;$j<$num_comps;$j++)
         {
             echo "<td align=\"center\">";
             if ($rankmat[$i][$j]==1) {echo "<b>";}
             echo $row["tp".$j]." <font size=\"2\">(".$rankmat[$i][$j].")</td>";
         }
         
         echo "</tr>";
         
      }
      
      echo "</table><br><br><br><br><h3>Special Awards</h3>";
      
      echo "<table class=\"specialcontainer\"><tr><td class=\"specialinfo\">";
      echo "<strong>The Corso Award</strong><br><p>For Excellence in Picking College Gameday Games</p><img src=\"images/corso.jpg\">";
      
      echo "</td><td>";
      echo "<table class=\"specialstanding\"><tr><th>Rank</th><th>Username</th><th>Correct Picks</th><th>Points Earned</th></tr>";
      
      $query = "select (select count(*) from question where pickname like \"%gameday%\" and correctans is not null) as tot, 
		count(*) as a, sum(pickpts), pick.username from pick, question 
		where pick.questionID = question.questionID and pickname like \"%gameday%\" and pickpts>0
		group by username order by a desc";
		
	  $result = $db->query($query);
	  $rank= 0;
	  $pts = 9999999;
	  for ($i=0;$i<$result->num_rows;$i++)
	  {
	  	$row = $result->FETCH_ASSOC();
	  	
	  	if ($row['a'] < $pts) {$rank=$i+1;$pts=$row['a'];}
	  	
	  	echo "<tr><td>".$rank."</td><td>".$row['username']."</td><td>".$row['a']."/".$row['tot'].
	  	"</td><td>".$row['sum(pickpts)']."</td></tr>";
	  }
	  
	  echo "</table></td></tr></table></br></br>";
	  
      echo "<table class=\"specialcontainer\"><tr><td class=\"specialinfo\">";
      echo "<strong>The Dierdorf Award</strong><br><p>For Excellence in Picking Sunday and Monday Night Games</p><img src=\"images/dierdorf.jpg\"></td><td>";
      
      echo "<table class=\"specialstanding\"><tr><th>Rank</th><th>Username</th><th>Correct Picks</th></tr>";
      
      $query = "select (select count(*) from question where picktype=\"ATS\" and bonusmult=2 and correctans is not null) as tot, 
		count(*) as a, pick.username from pick, question 
		where pick.questionID = question.questionID and picktype=\"ATS\" and bonusmult=2 and pickpts>0
		group by username order by a desc";
		
		
	  $result = $db->query($query);
	  $rank= 0;
	  $pts = 9999999;
	  for ($i=0;$i<$result->num_rows;$i++)
	  {
	  	$row = $result->FETCH_ASSOC();
	  	
	  	if ($row['a'] < $pts) {$rank=$i+1;$pts=$row['a'];}
	  	
	  	echo "<tr><td>".$rank."</td><td>".$row['username']."</td><td>".$row['a']."/".$row['tot'].
	  	"</td></tr>";
	  }
	  
	  echo "</table></td></tr></table></br></br>";
	  
	  echo "<table class=\"specialcontainer\"><tr><td class=\"specialinfo\">";
 	  echo "<strong>The Lester 'Worm' Murphy Award</strong><br><p>For Most Profitable Picks Against the Spread (NCAA and NFL)</p><img src=\"images/worm.jpg\"></td><td>";
      
      echo "<table class=\"specialstanding\"><tr><th>Rank</th><th>Username</th><th>Avg Profit on $100 bet (-110)</th><th>Correct Picks</th></tr>";
      
      $query = "select (select count(*) from question where (picktype=\"ATS\" or picktype=\"ATS-C\") and correctans is not null) as tot, 
		count(*) as a, pick.username from question, pick where pick.questionID = question.questionID and 
		(picktype=\"ATS\" or picktype=\"ATS-C\") and pick.pickpts>0 group by username order by a desc";
		
	  $result = $db->query($query);
	  $rank= 0;
	  $pts = 9999999;
	  for ($i=0;$i<$result->num_rows;$i++)
	  {
	  	$row = $result->FETCH_ASSOC();
	  	
	  	if ($row['a'] < $pts) {$rank=$i+1;$pts=$row['a'];}
	  	
	  	$return = ($row['a']*190.90 - $row['tot']*100)/$row['tot'] ;
	  	
	  	echo "<tr><td>".$rank."</td><td>".$row['username']."</td><td>".money_format('%n',$return)."</td><td>".$row['a']."/".$row['tot'].
	  	"</td></tr>";
	  }
	  
	  echo "</table></td></tr></table></br></br>";
	  
	  
      echo "<table class=\"specialcontainer\"><tr><td class=\"specialinfo\">";
      
       	  echo "<strong>The Richard Hatch Award</strong><br><p>For Best Winning Margin in Survivor Picks</p><img src=\"images/hatch.jpg\"></td><td>";
      
      echo "<table class=\"specialstanding\"><tr><th>Rank</th><th>Username</th><th>Average Winning Margin</th><th>Total Picks</th></tr>";
      
      $query = "select a.username, sum(a.margin) as totalmargin, count(*) as totalpicks, 
	sum(a.margin)/count(*) as avgmargin from 
	(select pick.pick, pick.username, game.ascore, game.hscore, pick.pickpts, 
	((pick.pickpts>0)*2-1)*abs(game.ascore-game.hscore) as margin
	from pick, question, game 
	where pick.questionID = question.questionID and (question.picktype=\"S-COL\" or question.picktype=\"S-PRO\")
	and (game.ateamID=pick.pick or game.hteamID=pick.pick) and game.ascore is not null
	and game.weeknum = question.weeknum) as a
	group by username order by avgmargin desc";
		
	  $result = $db->query($query);
	  $rank= 0;
	  $pts = 9999999;
	  for ($i=0;$i<$result->num_rows;$i++)
	  {
	  	$row = $result->FETCH_ASSOC();
	  	
	  	if ($row['a'] < $pts) {$rank=$i+1;$pts=$row['a'];}
	  	
	  	echo "<tr><td>".$rank."</td><td>".$row['username']."</td><td>".number_format($row['avgmargin'],2)."</td><td>".$row['totalpicks'].
	  	"</td></tr>";
	  }
	  
	  echo "</table></td></tr></table></br></br>";
     
  }
  
  

}

?>