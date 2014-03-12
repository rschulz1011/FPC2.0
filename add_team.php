<?php

session_start();

require("add_team_page.php");

$page = new Add_Team_Page();

if (isset($_GET['teamID'])) {
   $page -> teamin = $_GET['teamID'];
}
   
   

if (isset($_POST['loc']))
{
     $league=$_POST['league'];
     $loc=$_POST['loc'];
     $nick=$_POST['nick'];
     $conf=$_POST['conf'];
     $div=$_POST['div'];
 
     @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
     
     if (strlen($loc)<2 | strlen($league)<2)  {$noloc = 1;} else {$noloc = 0;}
     
     
     if (isset($_GET['upteam'])) {
     $query = "update team set location='".$loc."', nickname='".$nick.
              "', league='".$league."', conference='".$conf.
              "', division='".$div."' where teamID=".$_GET['upteam'];
      $suc_str = "updated"; 
     }
     else
     {     
     $query = "insert into team (location,nickname,league,conference,division)
             values ('".$loc."','".$nick."','".$league."','".$conf."','".$div."')";
      $suc_str ="added";
     }
     
     if ($noloc==0)
     
     {
        $result = $db->query($query);
     
        $teamadded = $db->affected_rows;
     
        $db->close();
     
        if ($teamadded) {$page->content="Team sucessfully ".$suc_str.": ".$loc." ".$nick."<br/>";}
        else {$page->content = "Database Error: Team not added";}
     }
     
     else
     
     {
        $page->content = "Error: Team must have location and league";
     }
     
}

$page->Display();


?>