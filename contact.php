<?php

   require("page.php");
   
   $homepage = new Page();
   
   $homepage -> content = "<p>The webmaster for this site can be reached at:</br>
   
      ryan.d.schulz@gmail.com</p>";
   
   
   $homepage->Display();