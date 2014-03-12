<?php

require("detailed_standings_page.php");

$page = new Detailed_Standings_Page();

if (isset($_GET['compID'])) {$page->compID=$_GET['compID'];}

$page->Display();

?>