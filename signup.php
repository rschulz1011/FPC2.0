<?php

   session_start();
   require("signup_page.php");
   
   $signuppage = new Signup_Page;
   
   $signuppage->content = "<br/>Your are now registered on FootballPickingChampionship.com!</br>
                    You will be redirected to your member home page! </br>
                    <meta http-equiv=\"refresh\" content=\"3;url=mhome.php\">" ;
   
   $signuppage -> Display();

  
?>