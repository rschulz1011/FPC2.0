<?php

session_start();

require "db_sync.php";
require "db.php";

$questionID = $_GET['questionID'];
    
$db = new Db();
update_question($questionID,$db);

?>