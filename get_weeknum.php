<?php

function get_weeknum($league,$date)

{
   if ($league=="NFL")
   {
      $epoch = "September 2, 2014 12:00 PM";
      $max_week = 17;
   }
   elseif ($league="NCAA")
   {
      $epoch = "August 26, 2014 12:00 PM";
      $max_week = 15;
   }
   
   $epoch_int = strtotime($epoch);
   
   if ($date=="now") {$now_int = now_time();}
   else {$now_int = strtotime($date);}
   
   $raw_weeks = ($now_int - $epoch_int)/604800;
   
   
   $whole_weeks = ceil($raw_weeks);
   
   
   if ($whole_weeks < 1) {$whole_weeks=1;}
   
   
   if ($whole_weeks>$max_week) {return 99;}
   else {return $whole_weeks;}
   
}

function now_time()
{
     return time()+3600*2;  //convert to EST
     //return strtotime("nov 30, 2013 9:00 pm");
}

?>