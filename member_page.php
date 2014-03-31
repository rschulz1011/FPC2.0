<?php

require("page.php");

class Member_Page extends Page
{

  //class Page's attributes
  
  public $content;
  public $title = "Football Picking Championship";
  public $keywords = "Football picking, pick'em";
  protected $db;
  
  public function __construct() {
  	$this->db = new Db();	
  }
  
  public function Display()
  {
     
     
     echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();
     
     $gooduser = $this -> authenticateUser();
     $this -> DisplayMenu();
     
     if ($gooduser)
     {
     echo "<hr>";
     echo $this->content;
     }
     else
     {
     echo $this->content;
     if ($gooduser==0) {$this-> LogInUser();}
     }
     $this -> DisplayFooter();
     echo "</body>\n</html>\n";
  }
  
  public function LogInUser()
  {
     ?>
     <form name="login" action="login.php" method="post">
        Username: <input type="text" name="user" /><br/>
        Password: <input type="password" name="pass" /><br/>
        <input type="submit" value="Submit"/>
     </form>
     <a class="passwordReset" href="password-reset.php">Forgot your password?</a>
     <br/>
     <font size="5">Or:</font></br>
     <a href="signup.php"><font size="5">Sign up now!</font></a><br/><br/><br/>
     <?php
  }

  
  public function authenticateUser()
  {
       if (isset($_SESSION['username']))
       {   
           $result = $this->db->authenticateUser($_SESSION['username'],$_SESSION['password']);
           
           $num_rows = $result->num_rows;
           
           if ($num_rows==1) {
                 echo "<font size=\"2\" color=\"red\">You are logged in as:&nbsp &nbsp ".$_SESSION['username'].
                   "&nbsp &nbsp<a href=\"logout.php\">LOG OUT</a></font>";
                 $row = $result->fetch_assoc();
                 $_SESSION['adminlev'] = $row['admin'];
                 return 1;
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
   
   public function ShowPosts($maxnumposts,$linkback)
   {
   
      echo "<br><br>";
      
              echo "<br> Post to Message Board!  <font size=\"2\"> limit 500 characters:</font><br>
        <form name=\"postcomment\" action=\"newpost.php\" method=\"post\">
        <textarea name=\"posttext\" cols=\"100\" rows=\"5\"></textarea><br>
        <input type=\"submit\" value=\"POST!\"/>
        <input name=\"linkback\" type=\"hidden\" value=\"".$linkback."\"></form>";
              
        $result = $this->db->getPosts($maxnumposts);
        
        $numposts = $result->num_rows;
        
        echo "Latest Posts:</br><table border=\"1\">";
        
        for ($i=0;$i<$numposts;$i++)
        {
             $row = $result->fetch_assoc();
             
             echo "<tr><td width=\"700\" border=\"0\"><font size=\"1\">Post by: <b>".$row['username']."</b> @ ".$row['posttime']."</font><br>";
             
             echo $row['posttext']."</td></tr>";
        
        }
        
        echo "</table><a href=\"viewposts.php\">See All Posts:</a><br>";
        

        
   
   }

  
}

?>
     
     