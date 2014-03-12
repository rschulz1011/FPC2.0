<?php

session_start();
require("add_questions_page.php");

class View_Questions_Page extends Add_Questions_Page
{

     public $selectorlink = "viewquestions.php";
            
    public function DisplayQuestionTable()
    {
            @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
           
           
           $query = "select * from (select a.location as aloc, h.location as hloc, game.gameID from game, 
           team as a, team as h where a.teamID=game.ateamID and h.teamID=game.hteamID) as g right join 
           question on question.gameID=g.gameID where question.weeknum='".$this->weeknum."' 
           and question.competitionID = '".$this->compID."'";


           $result = $db->query($query);
           $numresults = $result->num_rows;
           
     
           echo "<table><th>QuestionID</th><th>Pick Type</th><th>Game</th><th>Name</th><th>Option 1</th><th>Option 2
              </th><th>Bonus</th><th>Lock Time</th></tr>";
              
          echo "<form name=\"delform\" action=\"deletequestion.php?num_result="
              .$numresults."\" method=\"post\">";
     
         for($i=0;$i<$numresults;$i++)
         {
             $row = $result->fetch_assoc();
             
             if ($i % 2)  {echo "<tr class=\"shaded\" align=\"center\">";}
             else {echo "<tr align=\"center\">";}
             
             echo "<td>".$row['questionID']."</td><td>".$row['picktype']."</td>";
             if ($row['gameID']>0) {echo "<td>".$row['hloc'].' vs. '.$row['aloc']."</td>";}
             else {echo "<td></td>";}
             echo "<td>".$row['pickname']."</td>";
             if ($row['gameID']>0) {echo "<td>".$row['hloc'].'</td><td>'.$row['aloc']."</td>";}
             else {echo "<td>".$row['option1']."</td><td>".$row['option2']."</td>";}
             echo "<td>".$row['bonusmult']."</td><td>".$row['locktime']."</td><td>
             <a href=\"editquestion.php?questionID=".$row['questionID']."\">Edit</a></td><td>
             <input type=\"checkbox\" name=\"delcheck".$i."\" value=".$row['questionID']."</td></tr>";
         }
         
                         echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>
             <input type=\"submit\" value=\"Delete Checked\" /></td></tr>";
        echo "</table>";
       
    
    
      $db->close();
     }
     
}


?>