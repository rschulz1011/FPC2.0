<?php

session_start();
require("page.php");
   
class Signup_Page extends Page 
{
   
   public $title = "Football Picking Championship - Sign Up";
   
   public function Display()
   {
       echo "<html>\n<head>\n";
       $this -> DisplayTitle();
       $this -> DisplayKeywords();
       $this -> DisplayStyles();
       echo "</head>\n<body>\n";
       $this -> DisplayHeader();
       $this-> DisplayMenu($this->buttons);
       
       if (isset($_POST['user'])){
           $gooduser = $this->TrySignup();
       }
       else
       {
           $gooduser = -1;
       }
       

       if ($gooduser==0) {echo $this->content;}
       if ($gooduser<=0) {$this->SignUpForm();}
       
     $this -> DisplayFooter();
     echo "</body>\n</html>\n";
       

   }
        
   public function TrySignup()
   {
      // set control variable to 1 (good)
      $goodsignup = 1;
      $error_string = "";
      
      //open Database
      @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
      
      // verify unique username
      $user = $_POST['user'];
      
      if (strlen($user)>30) {$goodsignup=0; $error_string=$error_string." Username must be 30 characters or less<br>";}
      if (strpos($user,'"')===false & strpos($user,'\\')===false & strpos($user,"'")===false & strpos($user,"/")===false) 
      {}
      else
      {$goodsignup=0; $error_string=$error_string."Username may not contain ' \" \\ / <br>";}
      
      $query = "select username from user where username='".$user."'";
      $result = $db->query($query);
      $num_rows = $result->num_rows;
      
      if ($num_rows!=0) {$goodsignup=0; $error_string=$error_string."Username is already registered</br>";}
      
      //verify password match
      $pass1 = $_POST['pass'];
      $pass2 = $_POST['passver'];
      if ($pass1!=$pass2) {$goodsignup=0; $error_string=$error_string."Passwords do not match. </br>";}
      
      //verify league invite key
      $lik = $_POST['lik'];
      if ($lik != "fpc2013") {$goodsignup=0; $error_string=$error_string."You do not have the correct
              league invite key.</br>";}
              
      //parse emailshare value
      if ($_POST['emailshare']=="yes") {$emailshare=1;} else {$emailshare=0;}
              
      if ($goodsignup==1)
      {
           $query = "insert into user values (\"".$user."\",\"".crypt($pass1,'7fji9NK@()fafe')."\",\"".
           $_POST['email']."\",\"".$_POST['fname']."\",\"".$_POST['lname']."\",".
           $emailshare.",\"".date('c')."\",0)";
           
      
           $result = $db->query($query);
           $query_work = $db->affected_rows;
           
           if ($query_work!=1) {$goodsignup=0; $error_string=$error_string."Sign up could not be completed. Try again later </br>".$query;}
           else 
           {echo $this->content;
           $_SESSION['username'] = $user;
           $_SESSION['password'] = crypt($pass1,'7fji9NK@()fafe');}
           $db->close();
           
      }
         
      
      if (strlen($error_string)>1) {$this->content = $error_string;}

      return $goodsignup;
  }
      
      
   
   public function SignUpForm()
   {
 
     echo "<table><tr>";
     echo "<form name=\"signup\" action=\"signup.php\" method=\"post\">";
     
     if (isset($_POST['user']))
     {echo "<td>Username:</td><td> <input type=\"text\" name=\"user\" value=\"".$_POST['user']."\"/></td></tr>";}
     else{echo "<td>Username:</td><td> <input type=\"text\" name=\"user\"/></td><td>30 Characters Max</tr>";}
     
     if (isset($_POST['email']))
     {echo "<td>E-mail Address:</td><td> <input type=\"text\" name=\"email\" value=\"".$_POST['email']."\"/></td>";}
     else{echo "<td>E-mail Address:</td><td> <input type=\"text\" name=\"email\"/></td>";}
     
     
     echo" <td>Share e-mail with others?</td><td>
     <input type=\"checkbox\" name=\"emailshare\" value=\"yes\"></td></tr>";
     
     if (isset($_POST['fname']))
     {echo "<td>First Name:</td><td> <input type=\"text\" name=\"fname\" value=\"".$_POST['fname']."\"/></td></tr>";}
     else{echo "<td>First Name:</td><td> <input type=\"text\" name=\"fname\"/></td></tr>";}
     
     if (isset($_POST['lname']))
     {echo "<td>Last Name:</td><td> <input type=\"text\" name=\"lname\" value=\"".$_POST['lname']."\"/></td></tr>";}
     else{echo "<td>Last Name:</td><td> <input type=\"text\" name=\"lname\"/></td></tr>";}
     
     if (isset($_POST['pass']))
     {echo "<td>Password:</td><td> <input type=\"password\" name=\"pass\" value=\"".$_POST['pass']."\"/></td></tr>";}
     else{echo "<td>Password:</td><td> <input type=\"password\" name=\"pass\"/></td></tr>";}

     if (isset($_POST['passver']))
     {echo "<td>Verify Password:</td><td> <input type=\"password\" name=\"passver\" value=\"".$_POST['passver']."\"/></td></tr>";}
     else{echo "<td>Verify Password:</td><td> <input type=\"password\" name=\"passver\"/></td></tr>";}
     
     echo "<tr></tr>";
     
     if (isset($_POST['lik']))
     {echo "<td>League Invite Key:</td><td> <input type=\"text\" name=\"lik\" value=\"".$_POST['lik']."\"/></td></tr>";}
     else{echo "<td>League Invite Key:</td><td> <input type=\"text\" name=\"lik\"/></td></tr>";}
     
     echo "<tr><td></td><td><input type=\"submit\" value=\"Sign Up!\"/></td></tr>
     </form>
     </table>
     <br/>";
    
    }
  
}
  
?>