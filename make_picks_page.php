<?php

session_start();
require("member_page.php");
require("get_weeknum.php");
require("db_sync.php");

class Make_Picks_Page extends Member_Page

{

public function Display()
{   
	$gooduser = $this -> authenticateUser();
     echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     $this -> AddScripts();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
     
     if ($gooduser)
     {
     $this -> DisplayMenu($this->memberbuttons);
     echo "<hr>";
     $this->DisplayPicksDiv();
     $this-> DisplayPicksForm();
     echo $this->content;
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

public function AddScripts()
{
	echo "<script src=\"http://code.jquery.com/jquery-latest.min.js\"
        type=\"text/javascript\"></script>";
	echo "<script src=\"js/pickform.js\" type=\"text/javascript\"></script>";
	echo "<script>
  		  $(document).ready(function() {
		  parameters = {";
	
	$db = new Db();
	if (isset($_GET['compID']))
	{
		$compID = $_GET['compID'];
		$_SESSION['last_compID'] = $compID;
	
		if (isset($_GET['weeknum']))
		{
			$weeknum = $_GET['weeknum'];
			$_SESSION['last_weeknum'] = $weeknum;
		}
		else
		{
			$query = "select league from competition where competitionID='".$compID."'";
			$result = $db->query($query);
			$row = $result->fetch_assoc();
			$league = $row['league'];
			$weeknum = get_weeknum($row['league'],"now");
			$_SESSION['last_weeknum'] = $weeknum;
			 
		}
	}
	elseif (isset($_POST['compID']))
	{
		$compID = $_POST['compID'];
		$weeknum = $_POST['weeknum'];
	
		$_SESSION['last_compID'] = $compID;
		$_SESSION['last_weeknum'] = $weeknum;
	}
	elseif (isset($_SESSION['last_compID']))
	{
		$compID = $_SESSION['last_compID'];
		$weeknum = $_SESSION['last_weeknum'];
	}
	else
	{
		$compID = 1;
		$weeknum = get_weeknum("NFL","now");
	}
	echo 'username: "'.$_SESSION['username'].'",';
	echo 'compId: "'.$compID.'",';
	echo 'weeknum: "'.$weeknum.'"';
	
	echo "	};
		buildPickTable(parameters);
	
	});</script>";
	
}

public function DisplayPicksDiv()
{
	echo '<div id="pickForm"></div>';
}

public function DisplayPicksForm()
{
       $db = new Db();
       
       //perform database sync if needed
       $query = "select * from pick,question where pick.questionID=question.questionID 
       and (question.picktype=\"ATS-C\" or question.picktype=\"ATS\") 
       and question.locktime<'".date("Y-m-d H:i:s",now_time())."' and pick.pick is null";
       
       $result = $db->query($query);
       $num_expired = $result->num_rows;
       
       if ($num_expired>0) {update_database($db);}
       
       
       if (isset($_GET['compID']))
       {
          $compID = $_GET['compID'];
          $_SESSION['last_compID'] = $compID;
          
          if (isset($_GET['weeknum']))
          {
             $weeknum = $_GET['weeknum'];
             $_SESSION['last_weeknum'] = $weeknum;
          }
          else
          {
             $query = "select league from competition where competitionID='".$compID."'";
             $result = $db->query($query);
             $row = $result->fetch_assoc();
             $league = $row['league'];
             $weeknum = get_weeknum($row['league'],"now");
             $_SESSION['last_weeknum'] = $weeknum;
             
          }
       }
       elseif (isset($_POST['compID']))
       {
          $compID = $_POST['compID'];
          $weeknum = $_POST['weeknum'];
          
          $_SESSION['last_compID'] = $compID;
          $_SESSION['last_weeknum'] = $weeknum;
       }
       elseif (isset($_SESSION['last_compID']))
       {
          $compID = $_SESSION['last_compID'];
          $weeknum = $_SESSION['last_weeknum'];
       }
       else
       {
          $compID = 1;
          $weeknum = get_weeknum("NFL","now");
       }
       
       $query = "select compname,competitionID,league from competition where active='1'";
       $result = $db->query($query);
       $numcomps = $result->num_rows;
       
       echo "<form name=\"compweekselect\" action=\"makepicks.php\" method=\"post\">";
       echo "<select name=\"compID\" value=\"\">";
       
      
       for ($i=0;$i<$numcomps;$i++)
       {
           $row = $result->FETCH_ASSOC();
           echo "<option ";
           if ($compID==$row['competitionID']) {echo " selected ";$league=$row['league'];}
           echo " value =\"".$row['competitionID']."\">".$row['compname']."</option>";
       }
       echo "</select>";
       
       echo "<select name=\"weeknum\" value=\"\">";
  
        $maxweek=17;
        $special="POST";
        
        for ($i=1;$i<=$maxweek;$i++)
        {
           echo "<option "; if($i==$weeknum){echo " selected ";}
           echo " value=\"".$i."\">".$i."</option>";
        }
        
        echo "<option ";if($weeknum==99){echo " selected ";}
        echo " value=\"99\">".$special."</option></select>";
        
        echo  "</select><input type=\"submit\" value=\"GO\"/></form>";
       
       


$query = "select pick.pickID, pick.pick, pick.confpts, pick.locktime, pick.pickpts, q.correctans, 
   q.bonusmult, q.questionID, q.picktype, q.aloc, q.hloc, q.option1, q.option2, q.pickname, q.spread, q.picktype 
   from (select question.pickname, question.competitionID, question.weeknum, question.picktype, 
   question.questionID, question.option1, question.option2, question.bonusmult, question.correctans,
   g.aloc, g.hloc, g.spread from (select a.location as aloc, h.location as hloc, game.gameID, game.spread 
   from game, team as a, team as h where a.teamID=game.ateamID and h.teamID=game.hteamID) as g right join question on 
   question.gameID=g.gameID) as q, pick where pick.questionID=q.questionID and pick.username='".
   $_SESSION['username']."' and q.competitionID='".$compID."' and q.weeknum='".$weeknum."' order by pick.pickID";
   
   $result = $db->query($query);
   $numpicks = $result->num_rows;
   
   $query2 = "select * from (select t.location, t.teamID, t.conference, p.pickID from (select pick.pick, pick.pickID 
   from pick,question where username='".$_SESSION['username']."' and pick.questionID=question.questionID 
   and question.competitionID='".$compID."' and (question.weeknum<>'".$weeknum."' or pick.locktime<'".date("Y-m-d H:i:s",now_time()).
   "')) as p right join  (select game.gameID, team.teamID, team.location, team.conference from team, 
   game where (team.teamID=game.ateamID or team.teamID=game.hteamID) and team.league='".$league."' and 
   game.weeknum='".$weeknum."' and game.KOtime>'".date("Y-m-d H:i:s",now_time()).
   "') as t  on p.pick=t.teamID) as a where a.pickID is null and a.conference != 'FCS' order by location";
   
   $ateamsresult = $db->query($query2);
   $numteams = $ateamsresult->num_rows;
   
   
   echo "<form name=\"makepicksform\" action=\"makepicks.php?numpicks=".$numpicks."\" method=\"post\">";
   
   echo "<table><tr><th></th><th>Game</th><th>Favorite</th><th>PICK</th><th>Conf</th><th>Locktime</th>
   <th>Points</th></tr>";
   
   $query5 = "select * from pick,question where pick.questionID=question.questionID 
     and pick.pickpts=0 and question.competitionID='".$compID."' and pick.username='".$_SESSION['username']."' 
     and question.weeknum<'".$weeknum."' and pick.pick !='-1'";
     $result5 = $db->query($query5);
     $wrong = $result5->num_rows;
   
   for ($i=0;$i<$numpicks;$i++)
   {
       $row = $result->fetch_assoc();
       
       if ($i % 2)  {echo "<tr class=\"shaded\" align=\"center\">";}
       else {echo "<tr align=\"center\">";}
       $this->Display_Pick_Menu($row,$i+1,$numpicks,$ateamsresult,$numteams,$db,$wrong);
       echo "</tr>";
   }
   
   echo "<tr><td></td><td></td><td></td><td><input type=\"submit\" value=\"SUBMIT\"/></td>
   <td></td><td><a href =\"mhome.php\">Member Home</a></td><td></td>
   <td><a href=\"viewpicks.php?compID=".$compID."&weeknum=".$weeknum
   ."\">League Picks</a></td></tr></table></form>";
       
}


private function Display_Pick_Menu($row,$index,$numpicks,$ateamsresult,$numteams,$db,$wrong)
{
   if ($row['picktype']=='ATS-C' || $row['picktype']=='ATS')
   {
       echo "<td>".$row['pickname']."</td><td>".$row['aloc']." @ ".$row['hloc']."</td><td>";
       if (!is_null($row['spread']))
       {
          if ($row['spread']>0) {echo $row['aloc']." by ".abs($row['spread']);}
          elseif ($row['spread']<0) {echo $row['hloc']." by ".abs($row['spread']);}
          else {echo "PK";}
       }
       
       echo "</td>";
       
       if (strtotime($row['locktime']) > now_time())
       {
          echo "<td><select name=\"pick".$index."\" value=\"\">";
          echo "<option ";
          if (is_null($row['pick'])) {echo "selected ";}
          echo " value=\"0\"></option>";

       
          echo "<option ";
          if ($row['pick']==$row['option1']) {echo "selected ";}
          echo " value=\"".$row['option1']."\">".$row['aloc']."</option>";
       
          echo "<option ";
          if ($row['pick']==$row['option2']) {echo "selected ";}
          echo " value=\"".$row['option2']."\">".$row['hloc']."</option>";
       }
        else
       {
           

           
          if($row['pick']==$row['correctans']) {$format="goodpick";}
            elseif ($row['correctans']==-1) {$format="pushpick";}
            elseif (is_null($row['correctans'])) {$format="pendingpick";}
            else {$format="badpick";}

           
           echo "<td class=\"".$format."\">";
           
           if ($row['pick']==$row['option1']) {echo $row['aloc'];}
           if ($row['pick']==$row['option2']) {echo $row['hloc'];}
           echo "</td>";
       }
       
       echo "</select></td>";
       
       if ($row['picktype']=='ATS-C')
       {
            if (strtotime($row['locktime']) > now_time())
            {
               echo "<td><select name=\"conf".$index."\" value=\"\">";
               echo "<option ";
               if (is_null($row['confpts'])) {echo " selected ";}
               echo "value=\"0\"></option>";
               for ($j=1;$j<=$numpicks;$j++)
               {
                   echo "<option ";
                   if ($row['confpts']==$j) {echo " selected ";}
                   echo " value=\"".$j."\">".$j."</option>";
               }
               echo "</select></td>";
            }
            else
            {
               echo "<td><input type=\"hidden\" name=\"conf".$index."\" value=\"".$row['confpts']."\">"
               .$row['confpts']."</td>";
            }
       }
       else
       {
           echo "<td></td>";
       }
           
       echo "<td>".date("D, M d g:i a",strtotime($row['locktime']))."</td>";
       
       
       echo "<td>".$row['pickpts']."</td>";
       
       echo "<input type=\"hidden\" name=\"pickID".$index."\" value=\"".$row[pickID]."\">";   
   }
   
   $ateamsresult->data_seek(0);
   
   if ($row['picktype']=='S-PRO' || $row['picktype']=='S-COL')
   {
   
      echo "<td>".$row['pickname']."</td><td></td><td></td>";
      
      if ($row['picktype']=="S-COL" & $index>($numpicks-$wrong) & $index>1) {$skippick=1;}
      else {$skippick=0;}
      

   
      if (strtotime($row['locktime']) > now_time() && $skippick==0)
      {
          echo "<td><select name=\"pick".$index."\" value=\"\">";
          echo "<option ";
          if (is_null($row['pick']) | $row['pick']==0) {echo "selected ";}
          echo " value=\"0\"></option>";
      
          for ($j=0;$j<$numteams;$j++)
          {
              $arow = $ateamsresult->FETCH_ASSOC();
              echo "<option ";
              if ($arow['teamID']==$row['pick']) {echo " selected ";}
              echo " value=\"".$arow['teamID']."\">".$arow['location']."</option>";
          }
          echo "</select></td>";
      }
      else
      {
         $query3 = "select location from team where teamID='".$row['pick']."'";
         $result3 = $db->query($query3);
         $row3 = $result3->FETCH_ASSOC();
         if ($result3->num_rows>0)
         {
              if($row['pickpts']>0) {$format="goodpick";}
              elseif(is_null($row['pickpts'])) {$format="pendingpick";}
              else {$format = "badpick";}
         echo "<td class=\"".$format."\">".$row3['location']."</td>";
         }
         else
         {echo "<td></td>";}
      }
          
          echo "<td></td>";
          echo "<td>".date("D, M d g:i a",strtotime($row['locktime']))."</td>";
          echo "<td>".$row['pickpts']."</td>";
          echo "<input type=\"hidden\" name=\"pickID".$index."\" value=\"".$row[pickID]."\">";   
      
   }
   
   if ($row['picktype']=="OTHER")
   {
   		
   		echo "<td></td><td>".$row['pickname']."</td><td></td>";
   		
   		if (strtotime($row['locktime'])>now_time() )
   		{
   			echo "<td><select name=\"pick".$index."\" value=\"\">";
   			          echo "<option ";
          if (is_null($row['pick']) | $row['pick']==0) {echo "selected ";}
          echo " value=\"0\"></option>";
          echo "<option value=\"1\" ";if ($row['pick']==1) { echo " selected ";}
          echo ">".$row['option1']."</option>";
		  echo "<option value=\"2\" ";if ($row['pick']==2) { echo " selected ";}
          echo ">".$row['option2']."</option>";
          
   		}
   		else
   		{
   			if($row['pick']==$row['correctans']) {$format="goodpick";}
            elseif ($row['correctans']==-1) {$format="pushpick";}
            elseif (is_null($row['correctans'])) {$format="pendingpick";}
            else {$format="badpick";}

           
           echo "<td class=\"".$format."\">";
           
           if ($row['pick']==1) {echo $row['option1'];}
           if ($row['pick']==2) {echo $row['option2'];}
           echo "</td>";
   		}
   		
   		echo "</select></td><td></td><td>".date("D, M d g:i a",strtotime($row['locktime']))."</td><td>".$row['pickpts']."</td>";
   		 echo "<input type=\"hidden\" name=\"pickID".$index."\" value=\"".$row[pickID]."\">";   
   	
   }
   
   
}

}

?>