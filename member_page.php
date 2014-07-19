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
   	 $gooduser = $this -> authenticateUser();
  	 echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
     echo "<html>\n<head>\n";
     $this -> DisplayTitle();
     $this -> DisplayKeywords();
     $this -> DisplayStyles();
     echo "</head>\n<body>\n";
     $this -> DisplayHeader();      
     $this -> DisplayMenu();
     
     if ($gooduser)
     {
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

	public function displayMenu()
	{
		echo "<font size=\"2\" color=\"red\">You are logged in as:&nbsp &nbsp ".$_SESSION['username'].
		"&nbsp &nbsp<a href=\"logout.php\">LOG OUT</a></font>";
		parent::displayMenu();
	}
  
  public function authenticateUser()
  {
       if (isset($_SESSION['username']))
       {   
           $result = $this->db->authenticateUser($_SESSION['username'],$_SESSION['password']);
           
           $num_rows = $result->num_rows;
           
           if ($num_rows==1) {
                 $row = $result->fetch_assoc();
                 $_SESSION['adminlev'] = $row['admin'];
                 setcookie('username',$_SESSION['username'],time()+3600*24*60);
                 setcookie('password',$_SESSION['password'],time()+3600*24*60);
                 return 1;
            }
             else
             {
                 $this->content = "Username or password is incorrect...<br>";
                 return 0;
             }
        } 
        elseif (isset($_COOKIE['username']))
        {
        	$result = $this->db->authenticateUser($_COOKIE['username'],$_COOKIE['password']);
        	
        	$num_rows = $result->num_rows;
        	
        	if ($num_rows==1)
        	{
        		$row = $result->fetch_assoc();
        		$_SESSION['username'] = $_COOKIE['username'];
        		$_SESSION['password'] = $_COOKIE['password'];
        		setcookie('username',$_COOKIE['username'],time()+3600*24*60);
        		setcookie('password',$_COOKIE['password'],time()+3600*24*60);
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
      
              echo "<div id=\"messageBoard\"><span> Post to Message Board! </span>  <font size=\"2\"> limit 500 characters:</font><br>
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
        
        echo "</div>";
        
        echo "</table><a href=\"viewposts.php\">See All Posts:</a><br>";
        

        
   
   }

  
}

?>