<?php


session_start();
require("page.php");

class Password_Reset_Page extends page
{
  public function Display()
  {
     echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
     $this -> authenticateUser();
     $this -> DisplayMenu($this->buttons);
     echo "<hr>";
	 echo $this->content;
     $this -> DisplayFooter();
     echo "</body>\n</html>\n";
  }
	

}


?>