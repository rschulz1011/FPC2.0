<?php

function update_database($db)
{
         update_college_survivor_wrong_picks($db,13);
  
         $query = "select question.questionID from question,competition where question.competitionID =
         competition.competitionID and competition.active = 1 order by question.locktime, question.questionID";
         
         $result = $db->query($query);
         $num_questions = $result->num_rows;
         
         for ($i=0;$i<$num_questions;$i++)
         {
            $row = $result->fetch_assoc();
            update_question($row['questionID'],$db);
         }
}

function update_college_survivor_wrong_picks($db,$compID)
{
    
    $query = "select username from whoplays where competitionID='".$compID."'";
    $uresult = $db->query($query);
    $numusers = $uresult->num_rows;

   for ($i=0;$i<$numusers;$i++)
   {
      $urow = $uresult->FETCH_ASSOC();
      
      for ($w=2;$w<=16;$w++)
      {
         $week=$w;
         if ($week>15) {$week=99;}
         
         $query5 = "select * from pick,question where pick.questionID=question.questionID 
         and pick.pickpts=0 and question.competitionID='".$compID."' and pick.username='".$urow['username']."' 
         and question.weeknum<'".$week."' and pick.locktime<'".date("Y-m-d H:i:s",now_time())."' and pick.pick !='-1'";
         $result5 = $db->query($query5);
         $wrong = $result5->num_rows;
         
         $query6 = "select pick.pickID from pick, question where pick.questionID=question.questionID and
         question.competitionID='".$compID."' and pick.username='".$urow['username']."' 
         and question.weeknum='".$week."' order by question.questionID";
         
         $presult = $db->query($query6);
         $numpicks = $presult->num_rows;
         
         for ($j=0; $j<$numpicks; $j++)
         {
            $row = $presult->FETCH_ASSOC();
            if ( ($numpicks-$wrong)<=$j)
            {
               $query = "update pick set pick='-1' where pickID='".$row['pickID']."'";
               $db->query($query);
            }
            
         }
         
         
      }
   }

}


function update_game($gameID,$db)
{
   //find questions associated with game
   //echo "update game ".$gameID."</br>";
   
   $query = "select question.questionID from question,game where question.gameID = game.gameID 
   and game.gameID = '".$gameID."'";
   
   $result = $db->query($query);
   $num_questions = $result->num_rows;
   
   //update questions
   for ($i=0;$i<$num_questions;$i++)
   {
        $row = $result->FETCH_ASSOC();
        update_question($row['questionID'],$db);
   }
   
   // find picks associated with game
   $query = "select pick.pickID from pick,question,game where pick.questionID=question.questionID and 
            (pick.pick=game.hteamID or pick.pick=game.ateamID) and 
             (question.picktype=\"S-PRO\" or question.picktype=\"S-COL\") and question.weeknum = game.weeknum 
             and game.gameID='".$gameID."'";
             
   //echo $query."</br>";
             
   $result = $db->query($query);
   $numpicks = $result->num_rows;
   
   //update picks
   for ($i=0;$i<$numpicks;$i++)
   {
      $row = $result->FETCH_ASSOC();
      update_pick($row['pickID'],$db);
   }
   
}


function update_question($questionID,$db)
{
     //echo "update question ".$questionID."</br>";
     
     $query = "select * from question,competition where question.questionID='".$questionID."' and 
     question.competitionID = competition.competitionID";
     $result = $db->query($query);
     $row = $result->FETCH_ASSOC();
     $locktime_q = $row['locktime'];
     
     if ($row['picktype'] == "ATS-C" | $row['picktype'] == "ATS" | $row['picktype'] == "OTHER")
     // Database update for ATS type picks
     {
     
        // get question information from database
        $query = "select * from question,game where question.gameID = game.gameID and 
                question.questionID = '".$questionID."'";
        $result = $db->query($query);
        $row = $result->fetch_assoc();
        
        // sync up locktime to KO timeout
        
        if ($row['picktype'] == "ATS-C" | $row['picktype'] == "ATS" )
        {
        $query = "update question set locktime='".$row['KOtime']."' where questionID='".$questionID."'";
        $db->query($query);

        $query = "update pick set locktime='".$row['KOtime']."' where questionID='".$questionID."'";
        $db->query($query);
     	}
     	else
     	{
     		$query = "update pick set locktime='".$locktime_q."' where questionID='".$questionID."'";
			echo $query;
     		$db->query($query);
     	}
     
        // check if game has been settled
        if (!is_null($row['hscore']) & !is_null($row['ascore']))
        {
            // fill in correct answer
            if (($row['hscore']+$row['spread'])>$row['ascore'])
            {
               $query2 = "update question set correctans = '".$row['hteamID']."' where questionID = '".
                   $questionID."'";
               $result2 = $db->query($query2);    
            }
            elseif(($row['hscore']+$row['spread'])<$row['ascore'])
            {
               $query2 = "update question set correctans = '".$row['ateamID']."' where questionID = '".
                   $questionID."'";
               $result2 = $db->query($query2);  
            }
            else
            {
               $query2 = "update question set correctans = '-1' where questionID = '".
                   $questionID."'";
               $result2 = $db->query($query2);  
            }
         }
         // find picks linked to question
         $query = "select pick.pickID, pick.pick, pick.username, pick.locktime from pick,question where pick.questionID=question.questionID
              and question.questionID='".$questionID."'";
         $result3 = $db->query($query);
         $numpicks = $result3->num_rows;
         
         
          // sync up associated picks
         
          for ($i=0;$i<$numpicks;$i++)
          {
              $pickrow = $result3->fetch_assoc();
              
              
                 // if pick is blank, fill in default pick
                 if ( (is_null($pickrow['pick']) | $pickrow['pick']==0) & strtotime($pickrow['locktime'])<now_time())
                 {
                     $query = "update pick set pick = '".$row['option2']."' where pickID='".$pickrow['pickID']."'";
                     $db->query($query);
                  
                     if ($row['picktype']=="ATS-C")
                     {
                         $confpts = find_min_avail_confpts($pickrow['username'],$row['competitionID'],$row['weeknum'],$db);
                         $query = "update pick set confpts = '".$confpts."' where pickID='".$pickrow['pickID']."'";
                         $db->query($query);
                      
                     }
                  
                 }
              
                 update_pick($pickrow['pickID'],$db);
                      
          }
          
     }
     
     else
     {
         //database update for survivor type picks
         
         // find locktime by finding latest game in week
         $query = "select max(game.KOtime) from game,team where game.hteamID = team.teamID and 
         team.league = '".$row['league']."' and game.weeknum = '".
             $row['weeknum']."'";
         $result = $db->query($query);
         $row = $result->FETCH_ASSOC();
         
         
         $query = "update question set locktime='".$row['max(game.KOtime)']."' where questionID='".$questionID."'";
         $db->query($query);
         
         $query = "update pick set locktime ='".$row['max(game.KOtime)']."' where questionID='".$questionID."' and
          (pick is null or pick='0')";
          $db->query($query);
         
         // update individual picks for this question
         $query = "select pick.pickID, pick.pick, pick.username from pick,question where pick.questionID=question.questionID
                  and question.questionID='".$questionID."'";
         $result3 = $db->query($query);
         $numpicks = $result3->num_rows;
         
         for ($i=0;$i<$numpicks;$i++)
         {  
            $pickrow = $result3->fetch_assoc();
            update_pick($pickrow['pickID'],$db);             
         }
         
         
     }
     
}

function find_min_avail_confpts($username,$competitionID,$weeknum,$db)
{
    $query = "select pick.confpts from pick,question where pick.questionID=question.questionID and 
         question.competitionID = '".$competitionID."' and question.weeknum='".$weeknum."' and 
         pick.username='".$username."'";
         
    $result = $db->query($query);
    $numresults = $result->num_rows;
    
    //echo $query."</br>";
    
    $confarray = array($numresults);
    
    for ($i=0;$i<$numresults;$i++)
    {
        $row = $result->FETCH_ASSOC();
        $confarray[$i] = $row['confpts'];
        //echo $row['confpts'];
        //echo $confarray[$i];
    }
    
    
    $min_conf = 1;
    
    while (in_array($min_conf,$confarray))
    {
       $min_conf++;
    }
    
    return $min_conf;
}

function update_pick($pickID,$db)
{
    
    //echo "update pick ".$pickID."</br>";
    
    
    $query = "select * from pick,question where pick.questionID=question.questionID and pick.pickID = '".$pickID."'";
    $result = $db->query($query);
    $row = $result->fetch_assoc();
    
    // update ATS-C type picks
    if ($row['picktype']=="ATS-C" & !is_null($row['correctans']))
    {
        if ($row['pick']==$row['correctans'])
        {
            $pickpts = $row['confpts']*$row['bonusmult'];
        }
        elseif ($row['correctans']==-1)
        {
            $pickpts = $row['confpts']*$row['bonusmult']/2;
        }
        else
        {
            $pickpts = 0;
        }
        
        $query = "update pick set pickpts='".$pickpts."' where pickID='".$pickID."'";
       
        $db->query($query);   
    }
    
    // update ATS type picks
    if ($row['picktype']=="ATS" & !is_null($row['correctans']))
    {
        if ($row['pick']==$row['correctans'])
        {
            $pickpts = 2*$row['bonusmult'];
        }
        elseif ($row['correctans']==-1)
        {
            $pickpts = 0;
        }
        else
        {
            $pickpts = -1*$row['bonusmult'];
        }
        
        $query = "update pick set pickpts='".$pickpts."' where pickID='".$pickID."'";
        $db->query($query);   
    }
    
    //update S-COL type picks
    if ($row['picktype']=="S-COL")
    {   
        
        if ( (is_null($row['pick']) | $row['pick']==0) & strtotime($row['locktime'])<now_time()) 
        {
             $query = "update pick set pickpts='-3' where pickID='".$pickID."'";
             $db->query($query);
        }
        else
        {
        //find relevant game
           $query = "select * from game where game.weeknum='".$row['weeknum']."' and (game.hteamID='".$row['pick'].
               "' or game.ateamID='".$row['pick']."')";
           $gameresult = $db->query($query);
           $gamerow=$gameresult->FETCH_ASSOC();
        
           if (!is_null($gamerow['hscore']) & !is_null($gamerow['ascore']))
           {
              // find winner of game
              if ($gamerow['hscore']>$gamerow['ascore'])  {$gamewinner=$gamerow['hteamID'];}
              else {$gamewinner=$gamerow['ateamID'];}
           
              // assign points
              if ($row['pick'] == $gamewinner)
              {
                  $pickpts = 3*$row['bonusmult'];
              }
              else
              {
                  $pickpts = 0;
              }
           
              $query = "update pick set pickpts='".$pickpts."' where pickID='".$pickID."'";
              $db->query($query);   

           }
           
           if (is_null($row['pick']))
           {
        
           }
           else
           {
            $query = "update pick set locktime = '".$gamerow['KOtime']."' where pickID='".$pickID."'";
            $db->query($query);
           }
           
        }
            
    }
    
    //update S-PRO type picks
    if ($row['picktype']=="S-PRO" & is_null($row['pick'])==0 & $row['pick']<>0)
    {
        //find relevant game
        $query = "select * from game where game.weeknum='".$row['weeknum']."' and (game.hteamID='".$row['pick'].
            "' or game.ateamID='".$row['pick']."')";
        $gameresult = $db->query($query);
        $gamerow=$gameresult->FETCH_ASSOC();
        
        if (!is_null($gamerow['hscore']) & !is_null($gamerow['ascore']))
        {
           // find winner of game
           if ($gamerow['hscore']>$gamerow['ascore'])  {$gamewinner=$gamerow['hteamID'];}
           elseif ($gamerow['ascore']>$gamerow['hscore']) {$gamewinner=$gamerow['ateamID'];}
           else {$gamewinner=-1; echo "test";}
           
           // assign points
           if (($row['pick'] == $gamewinner) | ($gamewinner==-1))
           {   
               $runpts = calculate_run_points($row['username'],$row['weeknum'],$db);
               $margin_pts = abs($gamerow['hscore'] - $gamerow['ascore'])/2;
               $pickpts = $runpts + $margin_pts;
           }
           else
           {
               $pickpts = 0;
           }
           
           $query = "update pick set pickpts='".$pickpts."' where pickID='".$pickID."'";
           $db->query($query);   

        }
        
            $query = "update pick set locktime = '".$gamerow['KOtime']."' where pickID='".$pickID."'";
            $db->query($query);
            
    }
    
    if ($row['picktype']=="OTHER" & !is_null($row['correctans']))
    {
        if ($row['pick']==$row['correctans'])
        {
            $pickpts = $row['bonusmult'];
        }
        elseif ($row['correctans']==-1)
        {
            $pickpts = $row['bonusmult']/2;
        }
        else
        {
            $pickpts = 0;
        }
        
        $query = "update pick set pickpts='".$pickpts."' where pickID='".$pickID."'";
        $db->query($query);   
    }
    
    
    update_total_points($row['username'],$row['competitionID'],$db);
    
}

function calculate_run_points($username,$weeknum,$db)
{

    $query = "select pick.pickpts from pick,question where question.picktype='S-PRO' 
      and pick.questionID=question.questionID and pick.username = '".$username."' and 
      question.weeknum<'".$weeknum."' order by question.weeknum desc";
      
    $result = $db->query($query);
    $numresults = $result->num_rows;
    
    $runpoints=1;
    
    $row = $result->FETCH_ASSOC();
    
    while ($row['pickpts']>0 & $runpoints<=$numresults)
    {
        $runpoints++;
        $row = $result->FETCH_ASSOC();
    }
    
    return $runpoints;    
}

function update_total_points($username,$competitionID,$db)
{
    $query = "select sum(pick.pickpts) from pick,question where pick.questionID = question.questionID and 
           question.competitionID='".$competitionID."' and pick.username='".$username."'";
           
    //       echo $query."</br>";
           
    $result = $db->query($query);
    $row = $result->FETCH_ASSOC();
    $totalpoints = $row['sum(pick.pickpts)'];
    
    $query = "update whoplays set totalpoints='".$totalpoints."' where competitionID='".$competitionID."' 
            and username='".$username."'";
            
    //echo $query."</br>";
            
    $db->query($query);
}

?>