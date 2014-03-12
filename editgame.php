<?php

session_start();
require('edit_game_page.php');
require("get_weeknum.php");
require("db_sync.php");

$page = new Edit_Game_Page();
//
//
if (isset($_POST['hteam']))
{  
 
    $error = "";
   
    $ateam=$_POST['ateam'];
    $hteam=$_POST['hteam'];
    $kotime=$_POST['KOtime'];
    $spread=$_POST['spread'];
    $ascore=$_POST['ascore'];
    $hscore=$_POST['hscore'];
    
    $gameID = $_GET['gameID'];
    
    @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
           
    $query = "select team.league from game,team where gameID='".$gameID."' 
           and team.teamID=game.hteamID";
    
    $result = $db->query($query);
    $row = $result->fetch_assoc();
    $league = $row['league'];
    
    if (strlen($kotime)>5)
    {
        $timeval =  strtotime($kotime);
        if ($timeval==false){$error="could not parse time/date";}
        else {$weeknum = get_weeknum($league,$kotime);}
    }
    else {$error = "No date/time entered";}
    
    $query = "update game set ateamID='".$ateam."', hteamID='".$hteam.
              "', KOtime='".date("Y-m-d H:i:s",$timeval)."', weeknum='".
              $weeknum."'";
              
    if (strlen($spread)>0)
    {
       $query = $query.", spread='".$spread."'";
    }
    
    if (strlen($ascore)>0)
    {
       $query = $query.", ascore='".$ascore."'";
    }
    
    if (strlen($hscore)>0)
    {
       $query = $query.", hscore='".$hscore."'";
    }
    
    $query = $query." where gameID='".$gameID."'";
    
    if (strlen($error)==0)
    {
        $result = $db->query($query);
        $gamechanged = $db->affected_rows;
        
        if ($gamechanged) 
        {$page->content="<g class=\"good\">Game Updated Sucessfully</g><br/>";}
        else
        {$error = "Unable to update game: Database Error";}
    }
    
    update_game($gameID,$db);
    
    if (strlen($error)>0)
    {$page->content="<g class=\"bad\">".$error."</g><br/>";}
    
}

$page->Display();

?>