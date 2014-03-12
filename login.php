<?php
   session_start();
   
   require("member_page.php");
   
     $loginpage = new Member_Page();
   
   if (isset($_POST['user']))
   {
   $_SESSION['username'] = $_POST['user'];
   $_SESSION['password'] = crypt($_POST['pass'],'7fji9NK@()fafe');
   $loginpage->content = "<meta http-equiv=\"refresh\" content=\"0;url=mhome.php\">";
   }
   else
   {
   $loginpage->content = "<br/><b>You are allready logged in...</b><br/>
        <a href=\"logout.php\">Log Out</a>";
   }
   
 
   $loginpage->Display();
   
   
?>