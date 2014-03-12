<?php

session_start();
require("view_picks_page.php");

$page = new View_Picks_Page();

if (isset($_GET['compID'])) 
{
    $page->compID = $_GET['compID'];
    $page->weeknum = $_GET['weeknum'];
}
else
{
    $page->compID = 1;
    $page->weeknum=1;
}

$page->Display();

?>