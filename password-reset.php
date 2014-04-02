<?php
require ("password_reset_page.php");
require("Mail-1.2.0/Mail.php");

$page = new Password_Reset_Page ();

$db = new Db();

if (isset ( $_GET ['email'] )) {

	$result = $db->getUserByEmail(filter_input(INPUT_GET,'email',FILTER_SANITIZE_EMAIL));
	
	if ($result->num_rows >0)
	{
		$row = $result->fetch_assoc();
		$userID = $row['username'];
		
		$from = "Football Picking Championship <webmaster@footballpickingchampionship.com>";
		$to = $row['fname']." ".$row['lname']."<".$row['email'].">";
		$subject = "Password Reset Instructions";
		$body = "Hello, ".$row['fname']."
   
		You have requested a password reset at footballpickingchampionship.com.
		Click on the link below to reset your password. You will be redirected to the matrimosaic
		website where you will be allowed to change your password.
   
		Password Reset Link:
		www.footballpickingchampionship.com/password-reset.php?hash=".crypt($_GET['email'],'75ifsjvLK927')."&userid=".$userID;
		
		$host = "smtpout.secureserver.net";
		$port = "80";
		$username = "webmaster@footballpickingchampionship.com";
		$password = "Penroad24.";
		
		$headers = array ('From' => $from,
				'To' => $to,
				'Subject' => $subject);
		$smtp = Mail::factory('smtp',
				array ('host' => $host,
						'port' => $port,
						'auth' => true,
						'username' => $username,
						'password' => $password));
		
		$mail = $smtp->send($to, $headers, $body);
		
		if (PEAR::isError($mail)) {
			echo "email fail";
			$page->content = "<p>" . $mail->getMessage() . "</p>";
		} 
		else 
		{
			$page->content =  "An e-mail has been sent to ".$_GET['email'].". <br/>
			Please check your e-mail and click on the provided link to reset your password.";
		}
		
	}

} 
else if (isset($_GET['userid']) && isset($_GET['hash']) )
{
	$row = $db->getUser($_GET['userid']);
	
	if (strcmp(crypt($row['email'],'75ifsjvLK927'),$_GET['hash'])==0)
	{
		$page->content = "<p>Password Reset Successful! <br/> Please set your new password now:";
		$page->DisplayPasswordUpdateForm($row);
	}
}
else if (isset($_POST['password']))
{
	$result = $db->getUserByEmail($_POST['oldemail']);
	$row = $result->fetch_assoc();
	 
	if (strcmp($row['password'],$_POST['oldpass'])==0)
	{
		$error = "";
		if (strlen($_POST['password'])<8)
		{$error = $error."Password must be at least 8 characters.";}
		if (strcmp($_POST['password'],$_POST['passwordconf'])!=0)
		{$error = $error." Passwords must match.";}

		if (strlen($error)>2)
		{
			$page->content =  "Error: ".$error;
			$page->DisplayPasswordUpdateForm($row);
		}
		else
		{
			$db->updatePassword($_POST['oldemail'],crypt($_POST['password'],'7fji9NK@()fafe'));

			$_SESSION['username'] = $_POST['username'];
			$_SESSION['password'] = crypt($_POST['password'],'7fji9NK@()fafe');
			
			header( 'Location: mhome.php' );
		}
		 
	}
} 
else 
{
	$page->content = "<h2>Reset Your Password</h2>
	<p>Enter your e-mail address into the form below. We'll send you an email with a temporary password.
	Use the temporary password to log in, and you will then be automaticall prompted to change your password.
	</p> 
	<form id=\"passwordReset\">
        E-mail address: <input type=\"text\" name=\"email\" /><br/>
        <input type=\"submit\" value=\"Submit\"/>
	</form>";
}

$page->Display ();

?>