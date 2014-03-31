<?php

session_start();
require("admin_page.php");
require("get_weeknum.php");

class View_Game_Page extends admin_page
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
      
      
     if ($gooduser>0)
     {
     $this -> DisplayMenu($this->memberbuttons);
     echo "<hr>";
     echo $this->content;
     $this -> ShowGames();
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
     
     
     public function ShowGames()
     {
        // Set up default entries to league view
        if (isset($_GET['gameview_league']))
        {
           $league_value = $_GET['gameview_league'];
           $week_value = $_GET['gameview_week'];
           
           $_SESSION['last_gameview_league']=$league_value;
           $_SESSION['last_gameview_week']=$week_value;
           
        }
        elseif (isset($_POST['gameview_league']))
        {
           $league_value = $_POST['gameview_league'];
           $week_value = $_POST['gameview_week'];
           
           $_SESSION['last_gameview_league']=$league_value;
           $_SESSION['last_gameview_week']=$week_value;
           
        }
        elseif (isset($_SESSION['last_gameview_league']))
        {
           $league_value = $_SESSION['last_gameview_league'];
           $week_value = $_SESSION['last_gameview_week'];
        }
        else
        {
           $league_value = "NCAA";
           $week_value = get_weeknum($league_value,"now");
        }
           
        echo "<form name=\"viewteamselect\" action=\"viewgames.php\" method=\"post\">
              <select name=\"gameview_league\" value=\"".$league_value."\">
                   <option";if($league_value=="NCAA"){echo " selected ";}
        echo " value=\"NCAA\">NCAA</option>";
                echo "<option ";if($league_value=="NFL"){echo "selected ";} 
        echo " value=\"NFL\">NFL</option></select>";
        
        echo "<select name=\"gameview_week\" value=\"".$week_value."\">";
        
        $maxweek=17;
        $special="POST";
        
        for ($i=1;$i<=$maxweek;$i++)
        {
           echo "<option "; if($i==$week_value){echo " selected ";}
           echo " value=\"".$i."\">".$i."</option>";
        }
        
        echo "<option ";if($week_value==99){echo " selected ";}
        echo " value=\"99\">".$special."</option></select>";
        
        echo  "</select><input type=\"submit\" value=\"GO\"/></form>";

        $result = $this->db->getGames($league_value,$week_value);
        $num_results = $result->num_rows;

        echo "<br/><table><th>GameID</th><th>Away Team</th><th>Home Team</th><th>KO Time</th><th>
              Spread</th><th>Away Score</th><th>Home Score</th><form name=\"delform\" action=\"deletegame.php?num_result="
              .$num_results."\" method=\"post\">";
        
        for ($i=0;$i<$num_results;$i++)
        {
           $row = $result->fetch_assoc();
           
           if ($i % 2)  {echo "<tr class=\"shaded\" align=\"center\">";}
           else {echo "<tr align=\"center\">";}
           
           echo "<td>".$row['gameID']."</td><td>".$row['aloc']."</td><td>".$row['hloc'].
           "</td><td>".$row['KOtime']."</td><td>".$row['spread']."</td><td>".
           $row['ascore']."</td><td>".$row['hscore']."</td><td><a href=\"editgame.php?gameID=".$row['gameID']."\">
           Edit</a></td><td><input type=\"checkbox\" name=\"delcheck".$i."\" value=".
           $row['gameID']."</td></tr>";
           
        }
        
                echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>
             <input type=\"submit\" value=\"Delete Checked\" /></td></tr>";
        echo "</table>";

    }

}

?>