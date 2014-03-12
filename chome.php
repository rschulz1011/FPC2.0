<?php

session_start();
require("competition_home_page.php");

$page = new Competition_Home_Page();

if (isset($_GET['compid'])) 
{
    $page->compID = $_GET['compid'];
}
else
{
    $page->compID = 1;
}

$page->Display();

?>