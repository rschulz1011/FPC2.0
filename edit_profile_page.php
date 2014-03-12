<?php

session_start();
require("member_page.php");


class Edit_Profile_Page extends Member_Page
{

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
     $this-> DisplayForm();
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

public function DisplayForm()
{
       @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
       'fpcdata','bB()*45.ab','fpcdata');
       
      $username=$_SESSION['username'];
      
      $query = "select * from user where username='".$username."'";
      $result = $db->query($query);
      $row = $result->fetch_assoc();
      
      if (isset($_POST['email']))  {$this->UpdateProfile($row,$db);}
      
      $result = $db->query($query);
      $row = $result->fetch_assoc();
      
     echo "<table><tr>";
     echo "<form name=\"signup\" action=\"editprofile.php\" method=\"post\">";
     
     echo "<td>Username:</td><td>".$row['username']."</td></tr>";
     
     echo "<td>E-mail Address:</td><td> <input type=\"text\" name=\"email\" value=\"".$row['email']."\"/></td>";
     
     
     echo " <td>Share e-mail with others?</td><td><input type=\"checkbox\" name=\"emailshare\" value=\"yes\"";
     
     if ($row['emailshare']==1) {echo " checked ";}
     
     echo "></td></tr>";
     
     echo "<td>First Name:</td><td> <input type=\"text\" name=\"fname\" value=\"".$row['fname']."\"/></td></tr>";
     
     echo "<td>Last Name:</td><td> <input type=\"text\" name=\"lname\" value=\"".$row['lname']."\"/></td></tr>";

     echo "<td>Old Password:</td><td> <input type=\"password\" name=\"oldpassword\"/></td></tr>";
     
     echo "<td>New Password:</td><td> <input type=\"password\" name=\"newpassword\"/></td></tr>";
     
     echo "<td>Confrim New Password:</td><td> <input type=\"password\" name=\"confpassword\"/></td></tr>";

     
     echo "<tr><td></td><td><input type=\"submit\" value=\"Update!\"/></td></tr>
     </form>
     </table>
     <br/>";
      
}

public function UpdateProfile($row,$db)
{
   $updates = "";
   
   if ($row['email']!==$_POST['email']) {$updates = $updates.", email='".$_POST['email']."'";}
   
   if ($row['emailshare']==0 & $_POST['emailshare']=="yes") 
   {
      $updates = $updates.", emailshare='1'";
   }
   
   if ($row['emailshare']==1 & $_POST['emailshare']=="") 
   {
      $updates = $updates.", emailshare='0'";
   }
   
   
   if ($row['lname']!==$_POST['lname']) {$updates = $updates.", lname='".$_POST['lname']."'";}
   if ($row['fname']!==$_POST['fname']) {$updates = $updates.", fname='".$_POST['fname']."'";}
   
   if (crypt($_POST['oldpassword'],'7fji9NK@()fafe')==$row['password'])
   {
       $error = "";
       if (strlen($_POST['newpassword'])<4) {$error = $error."New Password must be 4 or more characters.<br/>";}
       if ($_POST['newpassword']!==$_POST['confpassword']) {$error = $error."New Passwords do not match.<br/>";}
       
       if (strlen($error)<2)  
       {
          $updates = $updates.", password='".crypt($_POST['newpassword'],'7fji9NK@()fafe')."'";
          $_SESSION['password'] = crypt($_POST['newpassword'],'7fji9NK@()fafe');
       }
       
   }
   
   if (strlen($updates)>2)
   {
   $updates[0] = ' ';
   $query = "update user set ".$updates." where username='".$row['username']."'";
   $db->query($query);

   }
   
   echo "<g class=\"bad\">".$error."</g>";

}

}