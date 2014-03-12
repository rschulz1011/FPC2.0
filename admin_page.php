<?php


require("member_page.php");

class Admin_Page extends Member_Page
{

   public $admin_level = 2;
   
   public function authenticateUser()
   {
        if (isset($_SESSION['username']))
        {
           @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
           
           $query = "select password,admin from user where username='".$_SESSION['username']."' 
           and  password='".$_SESSION['password']."'";
           
           $result = $db->query($query);
           
           $num_rows = $result->num_rows;
           
           if ($num_rows==1) {
                 echo "<font size=\"2\" color=\"red\">You are logged in as:&nbsp &nbsp ".$_SESSION['username'].
                   "&nbsp &nbsp<a href=\"logout.php\">LOG OUT</a></font>";
                  
                  $row = $result->FETCH_ASSOC();
                  $_SESSION['adminlev'] = $row['admin'];
                  
                 if ($row['admin'] >= $this->admin_level) { return 1;}
                 else { $this->content = "You are not authorized to view this page."; return -1;}
                 
            }
             else
             {
                 $this->content = "Username or password is incorrect...<br>";
                 return 0;
             }
        } 
        else
        {
            $this->content = "<br/><b>You are not logged in.</b><br>Log in now:<br/<br/>";
           return 0;
        }
       
   }

}
