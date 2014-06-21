<?php
   session_start();
   
   unset($_SESSION['username']);
   unset($_COOKIE['username']);
   setcookie("username","",-1);
   setcookie("password","",-1);
   
   require("member_page.php");
   
   $loginpage = new Member_Page();
   
   $loginpage->content = "";

   $loginpage->Display();
   
   
   
?>