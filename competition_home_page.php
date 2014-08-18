<?php

session_start();
require("member_page.php");
require("get_weeknum.php");

class Competition_Home_Page extends Member_Page
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
    $result = $this->db->getCompetition($this->compID);
    $row = $result->fetch_assoc();
    
    echo "<h2>".$row['compname']." - ".$row['year'];
    if ($row['active']==0) {echo " (Archived) ";}
    
    $league = $row['league'];
    
    echo "</h2>";
       
    echo "<div id=\"compHomeStandings\">";
    
    //standings table
    echo "<p class=\"compHomeTitle\">Current Standings (Top 10)</p>";
    
    $result = $this->db->getSimpleStandings($this->compID);
    $num_results = $result->num_rows;

    echo "<table><tr><th>Rank</th><th>Name</th><th>Score</th></tr>";
    
    $prevnum = null;
    $currank = 0;
    for ($i=0;$i<$num_results;$i++)
    {
       $row = $result->fetch_assoc();
       
       if ($prevnum==$row['totalpoints']) {$rank = $currank+1;}
       else {$currank=$i; $rank = $currank+1;}
       
       $prevnum = $row['totalpoints'];
       
       if ($i<10)
       {
       echo "<tr><td align=\"center\">".$rank."</td><td>";
       if ($row['username']==$_SESSION['username']) {echo "<b>";}
       echo "<a href=\"uinfo.php?userID=".$row['username']."\">".
       $row['username']."</a></td><td align=\"right\">".$row['totalpoints']."</td></tr>";
       }
       
       if ($_SESSION['username']==$row['username']) {$userpoints=$row['totalpoints'];$userrank=$rank;}
       
    }
    echo "</table>";
    
    echo "<a href=detailedstandings.php?compID=".$this->compID.">Detailed Standings</a>";
    
    echo "</div><div class=\"compHomeWeekly\">";
    
    //week-by-week
    echo "<p class=\"compHomeTitle\"> Your Total Points : ".$userpoints." (".$userrank."/".$num_results.")</p>"; 
    
    if ($league=="NCAA") {$maxweek=15;$special="BOWL";}
    else {$maxweek=17;$special="POST";}
    
    $result = $this->db->getPointsByWeek($_SESSION['username'],$this->compID);
    $num_results = $result->num_rows;
    $curweek = get_weeknum($league,"now");
    
    echo "<div id=\"compHomeWeeklyDetails\">";
    
    for ($i=0;$i<=$maxweek;$i++)
    {
        
        echo "<div class=\"weeklyDetail ";
        
        if (($i+1)==$curweek) {echo "highlight"; }
        
        echo "\"><p class=\"weekNum\">";
        if ($i<$maxweek) {echo "Week ".($i+1);} else {echo $special;}
        echo "</p><p class=\"weekPts\"><b>";
        
        if ($i<$num_results)
        {
           $row = $result->fetch_assoc();
           if (!is_null($row['sum(pick.pickpts)'])) {echo $row['sum(pick.pickpts)']." pts";}
           else {echo "0 pts";}
           echo "</b></p><a class=\"makePicks\" href=\"makepicks.php?compID=".$this->compID."&weeknum=".($i+1).
              "\">Your </br> Picks</a><a class=\"viewPicks\" href=\"viewpicks.php?compID=".$this->compID."&weeknum=".($i+1).
              "\">League </br> Picks</a>";
        }
        else
        {
           echo "--";
           echo "</b></p><td>";
        }
        
        echo "</div>";
    }
    
    echo "</div>";
}


}

?>