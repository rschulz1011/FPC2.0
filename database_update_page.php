<?php

session_start();
require("admin_page.php");

class Database_Update_Page extends Admin_Page
{

   public $admin_level = 2;
   
     public $memberbuttons = array("Home"=>"index.php",
                          "Admin Home"=>"adminhome.php",
                          "Member Home"=>"mhome.php",
                          "Competitions"=>"comps.php",
                          "Current Standings"=>"mem_standings.php",
                          "Contact Us"=> "contact.php",);
   
      public function Display()
      {
      	$gooduser = $this -> authenticateUser();
                echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
     
     
      
      
     if ($gooduser>0)
     {
     $this -> DisplayMenu($this->memberbuttons);
     echo $this->content;
     $this -> DisplayBody();
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
     
     
     public function DisplayBody()
     {
         $db = new Db();         
         update_database($db);
     }
     

}
