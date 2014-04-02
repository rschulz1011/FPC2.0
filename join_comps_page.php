<?php

session_start();
require("member_page.php");

class Join_Comps_Page extends Member_Page
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
     $this -> JoinForm();
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
     
     public function JoinForm()
     { 
       $username=$_SESSION['username'];

       $result = $this->db->getWhoPlays($username);
       $num_results = $result->num_rows;
       
       echo "<form name=\"delform\" action=\"joincomps.php?joinup=1\" method=\"post\"><table>";
       
       for ($i=0;$i<$num_results;$i++)
       {
           $row = $result->fetch_assoc();
           echo "<tr><td><b>".$row['compname']."</b></td>";
           if (is_null($row['totalpoints']))
           {
              echo "<td><b>Join!</b> <input type=\"checkbox\" name=\"joincheck".$i."\" value ="
                 .$row['competitionID']."></td></tr>";
           }
           else
           {
              echo "<td>Joined</td></tr>";
           }   
       }
       
       echo "<tr><td></td><td></td><td><input type=\"submit\" value=\"Join!\" /></td></tr></table></form>";
       
       echo "</br><a href=\"mhome.php\">Home</a>";
             
     }
     
}
     