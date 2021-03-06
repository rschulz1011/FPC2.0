<?php

session_start();
require("add_games_page.php");

class Edit_Game_Page extends Add_Games_Page
{
     
     public $gameID;
     
     public function Display()
     {
     	$gooduser = $this -> authenticateUser();
                echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
       
     if ($gooduser>0)
     {
     $this -> DisplayMenu($this->memberbuttons);
     echo $this->content;
     $this -> EditGameForm();
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
     
     
     public function EditGameForm()
     {
           $this->gameID = $_GET['gameID'];
       
           $result = $this->db->getGame($this->gameID);
           
           $row = $result->fetch_assoc();
           
           $league = $row['league'];
           
           $teamresult = $this->db->getTeamsByLeague($league);
           
           echo "<table><form name=\"editgame\" action=\"editgame.php?gameID=".$this->gameID."\" method=\"post\">
                <tr><td>Away Team:</td><td>";
                
           $this->MakeTeamSelector($teamresult,"ateam",$row['ateamID']);
           echo "</td></tr><tr><td>Home Team:</td><td>";
           $this->MakeTeamSelector($teamresult,"hteam",$row['hteamID']);
           
           echo "</td></tr><tr><td>Kickoff:</td><td><input type=\"text\" name=\"KOtime\" 
               value=\"".$row['KOtime']."\" size=\"35\"></td></tr>";
           
           echo "<tr><td>Week #:</td><td>".$row['weeknum']."</td></tr>";
           
           echo "<tr><td>Spread:</td><td><input type=\"text\" name=\"spread\" size=\"3\" value=\"".
               $row['spread']."\"></td></tr>";
               
           echo "<tr><td>Away Score:</td><td><input type=\"text\" name=\"ascore\" size=\"3\" value=\"".
               $row['ascore']."\"></td></tr>";
               
           echo "<tr><td>Home Score:</td><td><input type=\"text\" name=\"hscore\" size=\"3\" value=\"".
               $row['hscore']."\"></td></tr>";

           echo "<tr><td>GameID:</td><td>".$this->gameID."</td></tr>";
           
           echo "<tr><td></td><td><input type=\"submit\" value=\"Update\"></td>";
           
           echo "<td><a href=\"viewgames.php?gameview_league=".$league."&gameview_week=".$row['weeknum'].
                "\">View Games</a></td>";
                
           echo "</table></form>";
     }
     
}
     