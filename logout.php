<?php
   session_start();
   
   unset($_SESSION['username']);
   
   require("member_page.php");
   
   $loginpage = new Member_Page();
   
   setcookie("username","",time()-1);
   setcookie("password","",time()-1);
   
   $loginpage->content = "";

   $loginpage->Display();
   
   
   
?>