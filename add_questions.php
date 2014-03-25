<?php

   session_start();
   require("add_questions_page.php");
   
   $page = new Add_Questions_Page();
   
   $db = new Db();
   
   for ($i=0;$i<16;$i++)
   {
       $b = "picktype".$i;
       if ($_POST[$b]>0)
       {
           if($_POST[$b]==1){$picktype="ATS";}
           elseif($_POST[$b]==2){$picktype="ATS-C";}
           elseif($_POST[$b]==3){$picktype="S-COL";}
           elseif($_POST[$b]==4){$picktype="S-PRO";}
           elseif($_POST[$b]==5){$picktype="OTHER";}
           
           $b = "game".$i;
           $game = $_POST[$b];
           
           $b = "name".$i;
           $name = $_POST[$b];
           
           $b = "optA".$i;
           $optA = $_POST[$b];
           
           $b = "optB".$i;
           $optB = $_POST[$b];
           
           $b = "bonus".$i;
           $bonus = $_POST[$b];
           
           $b = "locktime".$i;
           $locktime = $_POST[$b];
           $timeval = strtotime($locktime);
           $locktime = date("Y-m-d H:i:s",$timeval);
           
           if ($picktype=="ATS" or $picktype=="ATS-C")
           {
               $pickData = $db->getPickDataAts($game);
           }
           elseif ($picktype=="S-COL" or $picktype=="S-PRO")
           {
           	   $league = $db->getLeague($_GET['compID']);
               $pickData = $db->getPickDataSurvivor($_GET['weeknum'],$league);          
           }
           elseif ($picktype=="OTHER")
           {
           	   $pickData['optA'] = $optA;
           	   $pickData['optB'] = $optB;
           	   $pickData['locktime'] = $locktime;
           }
           
           if ($bonus<1) {$bonus=1;}
           
           $questionID = $db->addQuestion($_GET['compID'],$pickData,$game,$name,$picktype,$_GET['weeknum'],$bonus);
		   $db->addPicks($questionID,$_GET['compID'],$pickData['locktime']);
                      
        }
   }
   
   $page->Display();
?>