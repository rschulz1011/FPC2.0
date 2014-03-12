<?php

require("db_sync.php");
require("database_update_page.php");
require("get_weeknum.php");

$page = new Database_Update_Page;

$page->content = "Database Updated!";

$page->Display();


?>