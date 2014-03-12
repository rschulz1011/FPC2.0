<?php
   session_start();
   require("add_games_page.php");
   require("get_weeknum.php");
   
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
               
               @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
                'fpcdata','bB()*45.ab','fpcdata');
                
                $query = "select location from team where teamID='".$ateam."'";
                $result = $db->query($query);
                $row = $result->FETCH_ASSOC();
                $ateam_name = $row['location'];
                
                $query = "select location from team where teamID='".$hteam."'";
                $result = $db->query($query);
                $row = $result->FETCH_ASSOC();
                $hteam_name = $row['location'];
                
                if (strlen($error)<3)
                {
                     $query = "select KOtime from game where ateamID='".$ateam."' and hteamID='".
                             $hteam."'";
                     $result = $db->query($query);
                     $num_rows = $result->num_rows;
                     if ($num_rows>0)
                     {
                        for ($j=0;$j<$num_rows;$j++)
                        {
                            $row = $result->FETCH_ASSOC();
                            if (($timeval)==strtotime($row['KOtime']))
                            { $error = "Game has allready been entered";}
                        }
                     }
                }
                
                if (strlen($error)<3)
                {
                   if (strlen($spread)>0)
                   {
                   $query = "insert into game (hteamID, ateamID, KOtime, weeknum, spread) values 
                            ('".$hteam."','".$ateam."','".date("Y-m-d H:i:s",$timeval)."','".$weeknum."','".$spread."')";
                   }
                   else
                   {
                   $query = "insert into game (hteamID, ateamID, KOtime, weeknum) values 
                            ('".$hteam."','".$ateam."','".date("Y-m-d H:i:s",$timeval)."','".$weeknum."')";
                   }
                   $result = $db->query($query);
                   $num_rows = $db->affected_rows;
                   
                   if ($num_rows!=1) {$error="Was not able to add to database";}
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
            $db->close();
              
           }
            
        }
        
       
   }
   
   
   $page->addgameleague = $_GET['addgameleague'];
   $page->Display();   
   
?>