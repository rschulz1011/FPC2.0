<?php

require("member_page.php");

class Admin_Page extends Member_Page
{

   public $admin_level = 2;
   protected $db;
   
   public function __construct()
   {
       $this->db = new Db();
   }
   
   public function authenticateUser()
   {
        if (isset($_SESSION['username']))
        {           
           $result = $this->db->authenticateUser($_SESSION['username'],$_SESSION['password']);
           
           $num_rows = $result->num_rows;
           
           if ($num_rows==1) {                  
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
