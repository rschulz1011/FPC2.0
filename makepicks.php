<?php
  session_start();
  require("make_picks_page.php");

  
  $page = new Make_Picks_Page();
  
  $error = "";
  
  if (isset($_GET['numpicks']))
  {
     $numpicks = $_GET['numpicks'];
     
     $numconf = 0;
     $numteams = 0;
     
     $pickidstr = "(";
     

     
     for ($i=0;$i<$numpicks;$i++)
     {
        $pick[$i] = $_POST["pick".($i+1)];
        $conf[$i] = $_POST["conf".($i+1)];
        
        if (!isset($pick[$i])) {$pick[$i]=0;}
        if (!isset($conf[$i])) {$conf[$i]=0;}
        
        $pickID[$i] = $_POST["pickID".($i+1)];

        if ($pick[$i]>0) {$numteams++;}
        if ($conf[$i]>0) {$numconf++;}
        $_SESSION["pick".($i+1)] = $pick[$i];
        $_SESSION["conf".($i+1)] = $conf[$i];
        if ($i > 0) {$pickidstr = $pickidstr.",";}
        $pickidstr = $pickidstr.$pickID[$i];
     }
     $pickidstr = $pickidstr.")";
     
     $pick[$numpicks] = 0;
     $conf[$numpicks] = 0;
     
     $uniqueconf = count(array_unique($conf))-1;

     if ($numconf>0 & $uniqueconf <> $numconf)
     { $error = $error."PICKS NOT SAVED! You may not have duplicate confidence points.</br>";}
     
      @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
       'fpcdata','bB()*45.ab','fpcdata');
       
     $query = "select pick.pickID, pick.pick, pick.confpts, question.picktype, question.weeknum,
     pick.locktime, question.questionID from pick, question where question.questionID = pick.questionID 
     and pick.pickID in ".$pickidstr;
     

     
     $result = $db->query($query);
     
     $row = $result->fetch_assoc();
     
     if ($row['picktype']=="S-PRO" | $row['picktype']=='S-COL')
     {
         $uniqueteams = count(array_unique($pick))-1;
         
     
         
         if ($uniqueteams<>$numteams) 
         {$error = $error."PICKS NOT SAVED! You may not have duplicate teams.</br>";}
     }
  
     
     
     if (strlen($error)<3)
     {
     
        for ($i=0;$i<$numpicks;$i++)
        {
           $result->data_seek(0);
           $row = $result->fetch_assoc();
     
           while ($row['pickID']<>$pickID[$i]) {$row = $result->fetch_assoc();}
           
           if (($row['pick']+1)<>($pick[$i]+1) | ($row['confpts']+1)<>($conf[$i]+1))
           {
           
              $locktime = $row['locktime']; 
           
              if ($row['picktype']=="S-PRO" | $row['picktype']=='S-COL')
              {
                  $query = "select KOtime from game where weeknum='".$row['weeknum']."' 
                  and (hteamID='".$pick[$i]."' or ateamID='".$pick[$i]."')";
                  
             
                  
                  $result2 = $db->query($query);
                  $row2 = $result2->fetch_assoc();
                  $locktime2 = $row2['KOtime'];
                  

                  
                  $locktime = $locktime2;
                  
              }
              
              
              if (now_time()<strtotime($locktime))
              {
                  $query = "update pick set pick='".$pick[$i]."', confpts='".$conf[$i]."', locktime ='".
                  $locktime."' where pickID='".$row['pickID']."'";
                  
                  $db->query($query);
              
              }
           
           }
         
        }
     
     }
  
  
  
  
  }
  
  
  if (strlen($error)>3)
  {$page->content = "<g class=\"bad\">".$error."</g><br/>";}
  elseif (isset($_GET['numpicks']))
  {$page->content = "<g class=\"good\">Picks Updated!</g><br/>";}
  
  $page->Display();
  
  
  ?>