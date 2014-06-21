<?php

session_start();
require("member_page.php");
require("get_weeknum.php");

class View_Picks_Page extends Member_Page
{

public $compID = 1;
public $weeknum = 1;

public function Display()
{    
	$gooduser = $this -> authenticateUser();
     echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
     

     
     if ($gooduser)
     {
     $this -> DisplayMenu($this->memberbuttons);
     $this-> DisplayBody();
     echo $this->content;
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

public function DisplayBody()
{
       echo "<h3>Week ".$this->weeknum." Picks</h3>";
       
    $db = new Db();
       
    $query = "select b.questionID, b.pickname, b.hloc, b.spread, team.location as aloc from (select a.questionID, a.pickname, a.gameID, 
    a.ateamID, a.spread, team.location as hloc from (select question.questionID, question.pickname, game.gameID, game.hteamID, 
    game.ateamID, game.spread from question left join game on game.gameID=question.gameID where 
    question.weeknum='".$this->weeknum."' and question.competitionID='".$this->compID."') as a left join 
    team on team.teamID=a.hteamID) as b left join team on b.ateamID=team.teamID order by b.questionID";
    
    
    $qresult = $db->query($query);
    $num_q = $qresult->num_rows;
    
    $qids = array($num_q);
    
    echo "<table><tr><th></th>";
    
    for ($i=0;$i<$num_q;$i++)
    {
       $row = $qresult->FETCH_ASSOC();
       
       echo "<th class=\"vpheader\">";
       
       if (is_null($row['hloc'])) {echo $row['pickname']."<br/>";}
       else 
       {
       		echo $row['aloc'];
       		if ($row['spread']>0) {echo "(-".$row['spread'].")";}
       		echo "</br>@</br>".$row['hloc'];
       		if ($row['spread']<=0) {echo "(".$row['spread'].")";}
       		echo "</br>";
       }
       
       echo "</th>";
       
       $qids[$i] = $row['questionID'];
    }
    
    echo "<th></th></tr>";
       
   $query = "select pick.username, pick.pick, pick.pickpts, pick.locktime, team.location, pick.confpts,
    question.picktype, question.questionID, question.correctans, question.option1, question.option2 from question,pick left join team on team.teamID=pick.pick where 
    pick.questionID=question.questionID and question.competitionID='".$this->compID."'  
    and question.weeknum='".$this->weeknum."'order by pick.username,  pick.questionID";
    
    $presult = $db->query($query);
    
    $num_picks = $presult->num_rows;
    
    
    $prev_username = null;
    $qmark = 0;
    
    for ($i=0;$i<=$num_picks;$i++)
    {
       $row = $presult->FETCH_ASSOC();
       if($row['username']!=$prev_username) 
       {
          if ($i>0) {echo " <td><b>".$points." pts</b></td></tr>";}
          echo "<tr align=\"center\" class=\"userpick\"><td><a href=\"uinfo.php?userID=".$row['username'].
          "\">".$row['username']."</a></td>";
          $qmark = 0;
          $points=0;
          $prev_username=$row['username'];
       }
       
       while ($qids[$qmark]!=$row['questionID'])
       {
          $qmark++;
          echo "<td></td>";
       }
       
       if (strtotime($row['locktime'])<now_time())
       {
          if ($row['picktype']=="ATS" | $row['picktype']=="ATS-C" | $row['picktype']=="OTHER")
          { 
              if (is_null($row['correctans'])) {$format="vppending";}
              elseif ($row['correctans']==$row['pick']) {$format="vpcorrect";}
              elseif ($row['correctans']==-1) {$format="vppush";}
              else {$format="vpwrong";}

          }
          elseif ($row['picktype']=="S-COL" | $row['picktype']=="S-PRO")
          {
              if ($row['pickpts']>0) {$format="vpcorrect";}
              elseif ($row['pickpts']==0 & !is_null($row['pickpts'])) {$format="vpwrong";}
              else {$format="vppending";}
          }

          
           echo "<td class=\"".$format."\">";
          
          if ($row['picktype']=="OTHER") 
          {
          	if ($row['pick']==1) {echo $row['option1'];} elseif ($row['pick']==2) {echo $row['option2'];}
          }
          else
          {
          echo $row['location'];
          }
          
          if ($row['picktype']=="ATS-C") {echo " (".$row['confpts'].")";}
          echo "</td>";
       }
       else
       {
           if ($row['pick']!=0) {$format="vphidden";}
           else {$format="vpempty";}
           echo "<td class=\"".$format."\">--</td>";
       }
       
       $qmark++;
       $points = $points+$row['pickpts'];
       
    }
    
    echo "<tr></table><br/><a href=\"makepicks.php?compID=".$this->compID."&weeknum=".$this->weeknum.
    	"\">Make Your Picks</a><br>";
    
   
}


}

?>