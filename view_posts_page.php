<?php

session_start();
require("member_page.php");
require("get_weeknum.php");

class View_Posts_Page extends Member_Page
{

public function Display()
{    
	$gooduser = $this -> authenticateUser();
     echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
     
     if ($gooduser)
     {
     $this -> DisplayMenu($this->memberbuttons);
     echo $this->content;
     $this->ShowPosts(100,"viewposts.php");
     }
     else
     {
     $this-> DisplayMenu($this->publicbuttons);
     echo $this->content;
     if ($gooduser==0) {$this-> LogInUser();}
     }
     $this -> DisplayFooter();
     echo "</body>\n</html>\n";
}

}

?>