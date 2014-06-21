<?php

session_start();
require("admin_page.php");
require("get_weeknum.php");

class Update_Scores_Page extends Admin_Page
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
     $this -> UpdateScoresForm();
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
     
     
     public function UpdateScoresForm()
     {
         $db = new Db();
                
         $nowtime = now_time();
         
         // games where spread is needed
         
         $query = "select game.gameID, a.location as aloc, h.location as hloc, game.KOtime, game.spread, 
         h.league, game.weeknum, game.ascore, game.hscore from game, team as a, team as h, question where 
         question.gameID=game.gameID and (question.picktype='ATS-C' or question.picktype='ATS') and 
         game.hteamID = h.teamID and game.ateamID = a.teamID and game.KOtime between '"
         .date("Y-m-d H:i:s",$nowtime)."' and '".date("Y-m-d H:i:s",$nowtime+604800)."' and game.spread 
         is null order by game.KOtime";
              
              
         $result1 = $db->query($query);
         
         $num_results1 = $result1->num_rows;
         
         // games where score is needed
         $result2 = $db->getScoreUpdates();
         $num_results2 = count($result2);
         
         $query = "select * from question where picktype='OTHER' and locktime<'".date("Y-m-d H:i:s",$nowtime)."' and correctans is null";
         
         $result3 = $db->query($query);
         
         $num_results3 = $result3->num_rows;
         
         echo "<h2>Games Needing Spread:</h2><h4>Home team favored is negative</h4>";
         
         echo "<form name=\"update_games\" action=\"updatescores.php?numspreads=".$num_results1.
            "&numscores=".$num_results2."&numother=".$num_results3."\" method=\"post\">";
         
         if ($num_results1==0)
         {
             echo "No games need spread.</br></br>";
         }
         else
         {
            echo "<table><tr><th>Game</th><th>KO time</th><th>Spread</th></tr>";
            for ($i=0;$i<$num_results1;$i++)
            {
                $this->WriteTableLine($result1->FETCH_ASSOC(),'spread',$i);
            }
         
            echo "</table>";
         }
         
         echo "<h2>Games Needing Scores:</h2>";
         
         if ($num_results2==0)
         {
            echo "No games need score updates.</br></br></br>";
         }
         else
         {
            echo "<table><tr><th>Game</th><th>KO time</th><th>Away</th><th>Home</th></tr>";
            for ($i=0;$i<$num_results2;$i++)
            {
                $this->WriteTableLine($result2[$i],'score',$i);
            }
            echo "</table>";
         }
         
         echo "<h2>Other Open Questions</h2>";
         
         if ($num_results3==0)
         {
         	echo "No other Open Questions</br></br>";
         }
         else
         {
         	echo "<table><tr><th>Name</th><th>Locktime</th><th>Option 1</th><th>Option 2</th><th>Correct Ans</th></tr>";
         	
         	for ($i=0;$i<$num_results3;$i++)
            {
            	$row = $result3->FETCH_ASSOC();
                echo "<tr><td>".$row['pickname']."</td><td>".$row['locktime']."</td><td>".$row['option1']."</td><td>".$row['option2']."</td><td>";
                echo "<input type=\"hidden\" name=\"otherID".$i."\" value=\"".$row['questionID']."\">";
                echo "<input type=\"text\" name=\"other".$i."\" size=\"1\"></td>";
                echo "</tr>";
            }
            
            echo "</table></br></br>";
         	
         }
           
         echo "<input type=\"submit\" value=\"Update Scores\"></form>";  
           
     }
     
     
     private function WriteTableLine($row,$type,$id)
     {
         
         echo "<tr><td>".$row['aloc']." @ ".$row['hloc']."</td><td>".
           $row['KOtime']."</td>"; 
         
         if ($type=="spread")
         {
            echo "<input type=\"hidden\" name=\"spgameID".$id."\" value=\"".$row['gameID']."\">";
            echo "<td><input type=\"text\" name=\"spread".$id."\" size=\"5\"></td>";
         }
         elseif ($type=="score")
         {
            echo "<input type=\"hidden\" name=\"scgameID".$id."\" value=\"".$row['gameID']."\">";
            echo "<td><input type=\"text\" name=\"ascore".$id."\" size=\"3\"></td>";
            echo "<td><input type=\"text\" name=\"hscore".$id."\" size=\"3\"></td>";
         }
         
         echo "</tr>";
     }
     
     
}