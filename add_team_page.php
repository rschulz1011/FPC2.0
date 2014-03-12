<?php

session_start();
require("admin_page.php");

class Add_Team_Page extends Admin_Page
{
      public $teamin = 0;
      
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
     $this -> DisplayTeamTable();
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
     
     public function DisplayTeamTable()
     {
          if ($this->teamin>0)
          {
            // perform database call to collect team info
           @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
           
           
           $query = "select location, nickname, league, conference, division 
           from team where teamID=".$this->teamin;
           
           $result = $db->query($query);
           
           $num_results = $result->num_rows;
           
           $row = $result->FETCH_ASSOC();
           
           $league = $row['league']; 
           $loc = $row['location'];
           $nick = $row['nickname'];
           $conf = $row['conference'];
           $div = $row['division'];
          }
          else
          {$league="";$loc="";$nick="";$conf="";$div="";}
          
          if ($this->teamin==0) {
          echo "<table><form name=\"addteam\" action=\"add_team.php\"";
          }
          else
          {
          echo "<table><form name=\"addteam\" action=\"add_team.php?upteam=".
          $this->teamin."\"";
          }
          
          echo "method=\"post\"><tr><td>League:</td><td><select name=\"league\">
                   <option value=\"\"></option>
                   <option ";
                   if ($league=="NFL"){echo " selected ";} 
                   echo "value=\"NFL\">NFL</option><option " ;
                   if ($league=="NCAA"){echo " selected ";}
                   echo " value=\"NCAA\">NCAA</option>
                </select></td></tr>";
                
          echo "<tr><td>Location:</td><td><input type=\"text\" name=\"loc\"
                value=\"".$loc."\"/></td></tr>";
                
          echo "<tr><td>Nickname:</td><td><input type=\"text\" name=\"nick\"
                value=\"".$nick."\"/></td></tr>";
          
          echo "<tr><td>Conference:</td><td><input type=\"text\" name=\"conf\"
                value=\"".$conf."\"/></td></tr>";
                
          echo "<tr><td>Division:</td><td><input type=\"text\" name=\"div\"
                value=\"".$div."\"/></td></tr>";
          if ($this->teamin==0)
          {
          echo "<tr><td></td><td><input type=\"submit\" value=\"Submit\"/></td>";
          }     
          else 
          {
            echo "<tr><td></td><td><input type=\"submit\" value=\"Update\"/></td>";
          }
          
          echo "<td><a href=\"viewteams.php\">View Teams</td></tr>
               </form></table>";
          
     }
      

}

?>