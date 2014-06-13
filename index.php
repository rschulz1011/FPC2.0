<?php

   require("page.php");
   
   $homepage = new Page();
   
   $homepage -> content = file_get_contents("html/homepage.html");
   
   
   $homepage->Display();
   
?>