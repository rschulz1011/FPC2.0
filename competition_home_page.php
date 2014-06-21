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
       
    echo "<table border=\"2\"><tr><td align=\"middle\" valign=\"top\">";
    
    //standings table
    echo "<h4>Current Standings (Top 10)</h4>";
    
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
    
    echo "<br><br><a href=detailedstandings.php?compID=".$this->compID.">Detailed Standings</a>";
    
    echo "</td><td align=\"center\">";
    
    //week-by-week
    echo "<h4> Your Total Points : ".$userpoints." (".$userrank."/".$num_results.")</h4>"; 
    
    if ($league=="NCAA") {$maxweek=15;$special="BOWL";}
    else {$maxweek=17;$special="POST";}
    
    $result = $this->db->getPointsByWeek($_SESSION['username'],$this->compID);
    $num_results = $result->num_rows;
    $curweek = get_weeknum($league,"now");
    
    echo "<table>";
    
    for ($i=0;$i<=$maxweek;$i++)
    {
        
        echo "<tr ";
        
        if (($i+1)==$curweek) {echo " class=\"highlight\" "; }
        
        echo "><td align=\"center\">";
        if ($i<$maxweek) {echo "Week ".($i+1);} else {echo $special;}
        echo "</td><td align=\"center\"><b>";
        
        if ($i<$num_results)
        {
           $row = $result->fetch_assoc();
           if (!is_null($row['sum(pick.pickpts)'])) {echo $row['sum(pick.pickpts)']." pts";}
           else {echo "0 pts";}
           echo "</b></td><td><a href=\"makepicks.php?compID=".$this->compID."&weeknum=".($i+1).
              "\">Your </br> Picks</a></td><td><a href=\"viewpicks.php?compID=".$this->compID."&weeknum=".($i+1).
              "\">League </br> Picks</a>";
        }
        else
        {
           echo "--";
           echo "</b></td><td></td><td></td>";
        }
        
        echo "</tr>";
    }
    
    echo "</table>";
    echo "</td></tr></table>";
   
}


}

?>