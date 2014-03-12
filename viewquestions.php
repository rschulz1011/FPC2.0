<?php

session_start();
require("view_questions_page.php");

$page = new View_Questions_Page();

$page->Display();

?>