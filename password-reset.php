<?php
require ("password_reset_page.php");

$page = new Password_Reset_Page ();

if (isset ( $_GET ['email'] )) {
	$page->content = "<p>We have sent you an email with your username and a temporary password.</p>";	
} else {
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