<?php
session_start();
require("page.php");

class Password_Reset_Page extends Page
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
	 echo $this->content;
     $this -> DisplayFooter();
     echo "</body>\n</html>\n";
  }
	
  
	public function DisplayPasswordUpdateForm($row)
	{
     
		$this->content = $this->content.
   		'<form id="passwordchange" name="passwordchange" method="post" action="password-reset.php">
   	    <p><label for="password" class="label">Password:</label>
	    <input type="password" name="password" id="password" value=""
	    />Password must be at least 8 characters long. </p></p>
	
	    <p><label for="passwordconf" class="label">Confirm Password:</label>
	    <input type="password" name="passwordconf" id="passwordconf" /></p>
   		<input type="submit" name="Submit" id="subscribe" value="Change Password" />
   		<input type="hidden" name="oldpass" value="'.$row['password'].'" />
   		<input type="hidden" name="username" value="'.$row['username'].'" />
   		<input type="hidden" name="oldemail" value="'.$row['email'].'" />
   		</form>';
   		
	}

}