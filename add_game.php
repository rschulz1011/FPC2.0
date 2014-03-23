<?php
   session_start();
   require("add_games_page.php");
   require("get_weeknum.php");
   
   $db = new Db();
   
   $page = new Add_Games_Page();

   if (isset($_GET['addgameleague']))
   {
        for ($i=0;$i<10;$i++)
        {  
           $error="";
           $ateam = $_POST["ateam".$i];
           $hteam = $_POST["hteam".$i];
           
           $spread = $_POST["spread".$i];

           if ($ateam>0&$hteam>0)
           {
               //check for valid date
               $d = "date".$i;
               $e = "time".$i;
               $timestr = $_POST[$d]." ".$_POST[$e];
               if (strlen($timestr)>5)
               {
                  $timeval =  strtotime($timestr);
                   if ($timeval==false){$error="could not parse time/date";}
                   else {$weeknum = get_weeknum($_GET['addgameleague'],$timestr);}
               }
               else {$error = "No date/time entered";}
               
               //check to make sure teams are not playing eachother
               if ($ateam==$hteam){$error="Teams can not play themselves";}
               
                $ateam_name = $db->getTeamName($ateam);
                
                $hteam_name = $db->getTeamName($hteam);
                
                if (strlen($error)<3)
                {
                	 $gameExists = $db->gameExists($ateam,$hteam,$timeval);
                     if ($gameExists) { $error = "Game has allready been entered";}
                }
                
                if (strlen($error)<3)
                {
                   $gameAdded = $db->addGame($hteam,$ateam,$timeval,$spread);
                   if (!$gameAdded) {$error="Was not able to add to database";}
                }
                     
                     
                
               if (strlen($error)<3)
               {
                    $page->content = $page->content."<g class=\"good\">".$ateam_name." vs. "
                    .$hteam_name.": Game Added Successfully </g><br/>";
               }
               else
               {
                   $page->content = $page->content."<g class=\"bad\">".$ateam_name." vs. ".
                   $hteam_name.": ".$error."</g><br/>";
              }
              
           }
            
        }
        
       
   }
   
   
   $page->addgameleague = $_GET['addgameleague'];
   $page->Display();   
   
?>