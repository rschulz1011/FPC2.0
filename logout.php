<?php
   session_start();
   
   unset($_SESSION['username']);
   
   require("member_page.php");
   
   $loginpage = new Member_Page();
   
   $loginpage->content = "";

   $loginpage->Display();
   
   
   
?>