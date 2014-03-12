<?php

session_start();
require("admin_page.php");

class Add_Questions_Page extends Admin_Page
{

     public $compID;
     public $weeknum;
     public $league;
     public $defaultpick;
     public $selectorlink = "add_questions.php";

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
     $this -> DisplaySelector();
     $this -> DisplayQuestionTable();
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
     
     function DisplaySelector()
     {   
           @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
           
           // set up default entries to question view
           if (isset($_GET['qview_comp']))
           {
               $this->compID = $_GET['qview_comp'];
               $this->weeknum = $_GET['qview_week'];
               
               $_SESSION['last_qview_comp'] = $this->compID;
               $_SESSION['last_qview_week'] = $this->weeknum;
            }
            elseif (isset($_POST['qview_comp']))
           {
               $this->compID = $_POST['qview_comp'];
               $this->weeknum = $_POST['qview_week'];
               
               $_SESSION['last_qview_comp'] = $this->compID;
               $_SESSION['last_qview_week'] = $this->weeknum;
            }
            elseif (isset($_SESSION['last_qview_comp']))
            {
               $this->compID = $_SESSION['last_qview_comp'] ;
               $this->weeknum = $_SESSION['last_qview_week'];
            }
            else
            {
               $this->compID = 1;
               $this->weeknum = 1;
            }
            
            $query = "select league,defaultpick from competition where competitionID='".$this->compID."'";
            $result = $db->query($query);
            $row = $result->fetch_assoc();
            $this->league = $row['league'];
            $this->defaultpick = $row['defaultpick'];
            
            $query = "select * from competition where active = 1";
            $result = $db->query($query);
            $num_results = $result->num_rows;
            
            echo "<form name=\"viewquestionselect\" action=\"".$this->selectorlink."?weeknum="
                     .$this->weeknum."&compID=".$this->compID."\" method=\"post\">
                     Competition:<select name=\"qview_comp\">";
                     
            for ($i=0;$i<$num_results;$i++)
            {
               $row = $result->fetch_assoc();
               echo "<option ";if($this->compID==$row['competitionID']){echo " selected ";} 
               echo " value =\"".$row['competitionID']."\">".$row['compname']."</option>";
            }
            
            echo "</select> Week: ";
            echo "<select name=\"qview_week\" value=\"".$this->weeknum."\">";
            
            $maxweek=17;
            $special="POST";
        
            for ($i=1;$i<=$maxweek;$i++)
           {
              echo "<option "; if($i==$this->weeknum){echo " selected ";}
              echo " value=\"".$i."\">".$i."</option>";
           }
           
           echo "<option ";if($this->weeknum==99){echo " selected ";}
           echo " value=\"99\">".$special."</option></select>";
           
           echo  "</select><input type=\"submit\" value=\"GO\"/></form>";
           $db->close();
    }
    
            
    public function DisplayQuestionTable()
    {
            @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
           
           $query = "select a.location, h.location, game.gameID from team as a, team as h, game where 
           game.weeknum=".$this->weeknum." and a.teamID=game.ateamID and h.teamID=game.hteamID and a.league='".$this->league."'";
           
           $result = $db->query($query);
     
       echo "<form name=\"addquestion\" action=\"add_questions.php?weeknum=".$this->weeknum.
            "&compID=".$this->compID."\" method=\"post\"><table>
            <th>Pick Type</th><th>Game*</th><th>Name*</th><th>Option 1*
            </th><th>Option 2*</th><th>Bonus</th><th>Lock Time*";
     
       
       for ($i=0;$i<16;$i++) 
       {
         echo "<tr><td><select name=\"picktype".$i."\">";
         echo "<option selected value=\"0\"></option>";
         
         $picktypes = array("ATS","ATS-C","S-COL","S-PRO","Other");
         
         for ($j=0; $j<sizeof($picktypes); $j++)
         {
             echo "<option value=\"".($j+1)."\">".$picktypes[$j];
             if ($this->defaultpick==$picktypes[$j]){echo "*";}
             echo "</option>";
         }
         
         echo "</select></td><td>";
         
         $this->MakeGameSelector($result,"game".$i,0);
         echo "</td><td><input size=\"30\" type=\"text\" name=\"name".$i."\"/></td>";
         echo "<td><input size=\"18\" type=\"text\" name=\"optA".$i."\"/></td>";
         echo "<td><input size=\"18\" type=\"text\" name=\"optB".$i."\"/></td>";
         echo "<td><input size=\"3\" type=\"text\" name=\"bonus".$i."\"/></td>";
         echo "<td><input size=\"20\" type=\"text\" name=\"locktime".$i."\"/></td>";
         

       }
       
       echo "<tr><td><td><td><td><td><input type=\"submit\" value=\"Add Questions\"></td>
           <td><a href=\"viewquestions.php\">View Questions</a></td></form></table>";
    
      $db->close();
     }
     
     public function MakeGameSelector($result,$menuname,$selected)
     {
            
            echo "<select name=\"".$menuname."\">";
            
            if ($selected==0) {echo "<option selected value=\"0\"></option>";};
            
            $num_rows = $result->num_rows;
            for ($i=0;$i<$num_rows;$i++)
            {
                $row = $result->fetch_array();
                echo "<option ";
                if ($row[2]==$selected) {echo " selected ";}
                echo " value=\"".$row[2]."\">".$row[0]." vs ".$row[1]."</option>";
            }
            
            echo "</select>";
            $result->data_seek(0);
     
     }
     
}