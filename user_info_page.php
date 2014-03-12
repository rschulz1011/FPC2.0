<?php

session_start();
require("member_page.php");

class User_Info_Page extends Member_Page
{

public $userID = '';

public function Display()
{    
     echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
     
     $gooduser = $this -> authenticateUser();
     
     if ($gooduser)
     {
     $this -> DisplayMenu($this->memberbuttons);
     echo "<hr>";
     $this-> DisplayBody();
     echo $this->content;
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
   
       @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
        'fpcdata','bB()*45.ab','fpcdata');
        
    $query = "select * from user where username = '".$this->userID."'";    
   
   $result = $db->query($query);
   
   $row = $result->FETCH_ASSOC();
   
   echo "<h3>User Information for ".$row['username']."</h3><br>";
   
   echo "<table><tr><td>Username:</td><td>".$row['username']."</td></tr>";
   
   echo "<tr><td>Name:</td><td>".$row['fname']." ".$row['lname']."</td></tr>";
   
   echo "<tr><td>Email: </td><td>";
   
   if ($row['emailshare']==1) {echo $row['email'];}
   else {echo "User does not share e-mail address";}
   
   echo "</td></tr></table>";

}

}


?>