<?php

session_start();
require("member_page.php");

class Detailed_Standings_Page extends Member_Page
{

public $compID = 1;

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
     echo "<hr>";
     $this-> DisplayStandings();
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

public function DisplayStandings()
{      
      $cresult = $this->db->getCompetition($this->compID);
      $row = $cresult->fetch_assoc();
      
      echo "<h3>Detailed Standings - ".$row['compname']."</h3>";
      
      if ($row['league'] == "NCAA")
      {$weeknums = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,99); $numweeks=16;}
      else
      {$weeknums = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,99); $numweeks=18;}
      
      $sresult = $this->db->getDetailedStandings($this->compID);      
      $numrecs = $sresult->num_rows;
      
      echo "<table><tr><th>Rank</th><th>Username</th><th>Total Score</th>";
      foreach($weeknums as $i)
      {
          if ($i==99)
          {echo "<th><font size=\"2\"> POST </th>";}
          else
          {echo "<th><font size=\"2\">Week ".$i."</th>";}
      }
      echo "</tr>";
      
      $cur_rec = 0; 
      $weekmax = array();
      for ($i=0; $i<$numweeks; $i++)
      {
        $weekmax[$i] = 0;
      }
      
      while ($cur_rec<$numrecs)
      {
          $row = $sresult->fetch_assoc();
          $cur_rec++;
          if ($weekmax[$row['weeknum']-1]<$row['sum(pick.pickpts)'])
          {$weekmax[$row['weeknum']-1] = $row['sum(pick.pickpts)']+1-1;}
      }
      
      mysqli_data_seek($sresult,0);
      
      $cur_user = null;
      $rank = 0;
      $nextrank = 0;
      $lastscore = 100000;
      
      for ($i=0;$i<$numrecs;$i++)
      {
           $row = $sresult->fetch_assoc();
           
           if ($row['username'] != $cur_user)
           {
               $nextrank++;
               $cur_user = $row['username'];
               if ($lastscore > $row['totalpoints']) {$rank = $nextrank; $lastscore=$row['totalpoints']; }
               echo "</tr><tr><td>".$rank."<td>".$row['username']."</td><td align=\"center\">".$row['totalpoints']."</td>";
               $nextweek = 1;
           }
           else
           {
              $nextweek++;
           }
           
           if ($row['weeknum']>$nextweek) {$nextweek++; echo "<td></td>";}
           
           echo "<td align=\"center\"><font size=\"2\">";
           
           if ($row['sum(pick.pickpts)'] == $weekmax[$nextweek-1]) {echo "<b>";}
           
           if (!is_null($row['sum(pick.pickpts)'])) {echo $row['sum(pick.pickpts)'];}
           else {echo 0;}
           
           if ($row['sum(pick.pickpts)'] == $weekmax[$nextweek-1]) {echo "</b>";}
           
           echo "</td>";
           
      }
      
      
    
    
}


}
