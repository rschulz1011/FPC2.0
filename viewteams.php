<?php

session_start();
require('view_team_page.php');

$page = new View_Team_Page();

$page->Display();

?>