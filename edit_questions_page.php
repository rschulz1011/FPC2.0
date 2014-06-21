<?php

session_start();
require("add_questions_page.php");

class Edit_Questions_Page extends add_questions_page
{
   
   public $questionID;
   
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
     $this -> EditQuestionForm();
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
     
     public function EditQuestionForm()
     {
         $this->questionID = $_GET['questionID'];
           
        $result = $this->db->getQuestion($this->questionID);
        
        $row = $result->FETCH_ASSOC();
        
        echo "<table><form name=\"editquestion\" action=\"editquestion.php?questionID=".$this->questionID.
        "\" method = \"post\"><tr><td>Question ID:</td><td>".$row['questionID']."</td></tr>";
        
        $result2 = $this->db->getCompetition($row['competitionID']);
        $row2 = $result2->FETCH_ASSOC();
        
        echo "<tr><td>Competition: </td><td>".$row2['compname']."</td></tr>";
        
        echo "<tr><td>Week Number: </td><td>".$row['weeknum']."</td></tr>";
        
        echo "<tr><td>Pick Type: </td><td>".$row['picktype']."</td></tr>";
        
        echo "<tr><td>Pick Name: </td><td><input type=\"text\" name=\"pickname\" size=\"15\" value =
        \"".$row['pickname']."\"></td></tr>";
        
           
        $query3 = "select a.location, h.location, game.gameID from team as a, team as h, game where 
           game.weeknum=".$row['weeknum']." and a.teamID=game.ateamID and h.teamID=game.hteamID and a.league='".$row2['league']."'";
           
        $result3 = $this->db->getGamesForWeek($row['weeknum'],$row2['league']);
        
        echo "<tr><td> Game: </td><td>";
        
        if ($row['gameID']>0)
          {
             $this->MakeGameSelector($result3,"gameID",$row['gameID']);
             echo "</td><tr>";
             
             echo "<tr><td>Option 1: </td><td>".$row['aloc']."</td></tr>";
             echo "<tr><td>Option 2: </td><td>".$row['hloc']."</td></tr>";
             
             echo "<tr><td>Correct Answer: </td><td><select name=\"correctans\">";
             
             echo "<option ";
             if ($row['correctans']==0) {echo " selected ";}
             echo " value=\"0\"></option>";
             
             echo "<option ";
             if ($row['correctans']==$row['option1']) {echo " selected ";}
             echo " value=\"".$row['option1']."\">".$row['aloc']."</option>";
             
              echo "<option ";
             if ($row['correctans']==$row['option2']) {echo " selected ";}
             echo " value=\"".$row['option2']."\">".$row['hloc']."</option>";
             
             echo "</select></td></tr>";
             
             echo "<tr><td>Lock Time: </td><td> ".$row['locktime']."</td></tr>";
             
          }
        else
        {
             echo "</td></tr>";
             echo "<tr><td>Option 1: </td><td><input type=\"text\" name=\"option1\" 
             value =\"".$row['option1']."\"</td></tr>";
             
             echo "<tr><td>Option 2: </td><td><input type=\"text\" name=\"option2\" 
             value=\"".$row['option2']."\"</td></tr>";
             
             echo "<tr><td>Correct Answer: </td><td><select name=\"correctans\">";
             
              echo "<option ";
             if ($row['correctans']==0) {echo " selected ";}
             echo " value=\"0\"></option>";
             
             echo "<option ";
             if ($row['correctans']==1) {echo " selected ";}
             echo " value=\"1\">".$row['option1']."</option>";
             
              echo "<option ";
             if ($row['correctans']==2) {echo " selected ";}
             echo " value=\"2\">".$row['option2']."</option>";
             
             echo "</select></td></tr>";
             
             echo "<tr><td>Lock Time: </td><td><input type=\"text\" name=\"locktime\" size=\"20\"
                  value=\"".$row['locktime']."\"></td></tr>";
             
        }
        
        echo "<tr><td>Bonus Multiplier: </td><td><input type=\"text\" name=\"bonusmult\" size=\"3\"
           value=\"".$row['bonusmult']."\"></td></tr>";
           
        echo "<tr><td></td><td><input type=\"submit\" value=\"Update\"></td>";
        
        echo "<td><a href=\"viewquestions.php?qview_comp=".$row['competitionID']."&qview_week=".$row['weeknum'].
                "\">View Questions</a></td>";
                
        echo "</table></form>";
           
     }
   
}