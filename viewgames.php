<?php

session_start();
require('view_games_page.php');

$page = new View_Game_Page();

$page->Display();

?>