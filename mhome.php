<?php
   
  session_start();
     require("member_home_page.php");
  
  $mhome = new Member_Home_Page();
  
  if ($_SESSION['adminlev']>0)
  { $mhome->content = $mhome->content."<a href=\"adminhome.php\">Admin Home</a><br/>";}
  
  $mhome->content = $mhome->content."<a href=\"editprofile.php\">Edit Your Profile</a><br/>";
  
  $mhome->Display();
  
?>