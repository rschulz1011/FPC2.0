<?php

session_start();
require("admin_page.php");

class Add_Games_Page extends Admin_Page
{
     
     public $addgameleague = "NCAA";
          
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
     echo "<hr>";
     echo $this->content;
     $this -> DisplayGameTable();
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
     
     function DisplayGameTable()
     {   
     
       $result = $this->db->getAllTeams($this->addgameleague);
           
       echo "<form name=\"addgame\" action=\"add_game.php?addgameleague=".$this->addgameleague.
            "\" method=\"post\"><table><th>Date</th><th>Time</th><th>Away Team</th><th>Home Team
            </th><th>Spread</th>";
     
       for ($i=0;$i<10;$i++) 
       {
         echo "<tr><td><input type=\"text\" name=\"date".$i."\"></td>";
         echo "<td><input type=\"text\" name=\"time".$i."\"></td>";
         echo "<td>";
         $this->MakeTeamSelector($result,"ateam".$i,0);
         echo "</td><td>";
         $this->MakeTeamSelector($result,"hteam".$i,0);
         echo "</td><td><input type=\"text\" name=\"spread".$i."\"></td></tr>";
       }
       
       echo "<tr><td><td><td><td><td><input type=\"submit\" value=\"Add Games\"></td>
           <td><a href=\"viewgames.php\">View Games</a></td></form></table>";   
     }
     
     public function MakeTeamSelector($result,$menuname,$selected)
     {
         echo "<select name=\"".$menuname."\">";
            
            if ($selected==0) {echo "<option selected value=\"0\"></option>";}
            
            $num_rows = $result->num_rows;
            for ($i=0;$i<$num_rows;$i++)
            {
                $row = $result->FETCH_ASSOC();
                echo "<option ";
                if ($row['teamID']==$selected) {echo " selected ";}
                echo " value=\"".$row['teamID']."\">".$row['location']."</option>";
            }
            
            echo "</select>";
            $result->data_seek(0);
     }
     
     
     
     
}
