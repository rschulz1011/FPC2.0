<?php

session_start();

require "db_sync.php";

$questionID = $_GET['questionID'];

         @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
                'fpcdata','bB()*45.ab','fpcdata');

update_question($questionID,$db);

?>