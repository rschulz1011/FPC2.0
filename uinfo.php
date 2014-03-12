<?php

session_start();

require("user_info_page.php");

$page = new User_Info_Page;

$page->userID = $_GET['userID'];

$page->Display();

?>
