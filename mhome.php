<?php
   
  session_start();
     require("member_home_page.php");
  
  $mhome = new Member_Home_Page();
  

  
  $mhome->Display();
  
?>