<?php

session_start();
require("admin_page.php");

class View_Team_Page extends admin_page
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
     $this -> ShowTeams();
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
     
     
     public function ShowTeams()
     {
        // Set up default entries to league view
        if (isset($_POST['teamview_league']))
        {
           $league_value = $_POST['teamview_league'];
           $conf_value = $_POST['teamview_conf'];
           
           $_SESSION['last_teamview_league']=$league_value;
           $_SESSION['last_teamview_conf']=$conf_value;
           
        }
        elseif (isset($_SESSION['last_teamview_league']))
        {
           $league_value = $_SESSION['last_teamview_league'];
           $conf_value = $_SESSION['last_teamview_conf'];
        }
        else
        {
           $league_value = "NCAA";
           $conf_value = "Big 10";
        }
        
        $result = $this->db->getConferences();
        $num_results = $result->num_rows;
        
        
        echo "<form name=\"viewteamselect\" action=\"viewteams.php\" method=\"post\">
              <select name=\"teamview_league\" value=\"".$league_value."\">
                   <option";if($league_value=="NCAA"){echo " selected ";}
        echo " value=\"NCAA\">NCAA</option>";
              
        echo "<option ";if($league_value=="NFL"){echo "selected ";} 
        echo " value=\"NFL\">NFL</option></select>";
        echo "<select name=\"teamview_conf\" value=\"".$conf_value."\">";
        
        for ($i=0; $i <$num_results; $i++) {
           $row = $result->FETCH_ASSOC();
           echo "<option ";
           if ($conf_value==$row['conference']){echo " selected ";}
           echo " value=\"".$row['conference']."\">".$row['conference']."</option>";
           }
           
        echo  "</select><input type=\"submit\" value=\"GO\"/></form>";

        $result = $this->db->getTeams($league_value,$conf_value);
        
        $num_results = $result->num_rows;
        
        echo "<br/><table><th>TeamID</th><th>Location</th><th>Nickname</th><th>League</th><th>
              Conference</th><th>Division</th><form name=\"delform\" action=\"deleteteam.php?num_result="
              .$num_results."\" method=\"post\">";
        
        for ($i=0; $i<$num_results; $i++) {
           $row = $result->FETCH_ASSOC();
           if ($i % 2)  {echo "<tr class=\"shaded\" align=\"center\">";}
           else {echo "<tr align=\"center\">";}
           echo "<td>".$row['teamID']."</td><td>".$row['location']."</td><td>".$row['nickname'].
                "</td><td>".$row['league']."</td><td>".$row['conference']."</td><td>".
                $row['division'],"</td><td><a href=\"add_team.php?teamID=".$row['teamID']."\">
                Edit</a></td><td><input type=\"checkbox\" name=\"delcheck".$i."\" value=".
                $row['teamID']."</td></tr>";
        }
        
        echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>
             <input type=\"submit\" value=\"Delete Checked\" /></td></tr>";
        echo "</table>";

    }

}

?>