<?php

session_start();
require("get_weeknum.php");
require("db.php");

if (strlen($_POST['posttext'])>=1)
{
    $db = new Db();

    $db->newPost($_POST['posttext'],$_SESSION['username']);
         
    header("Location: ".$_POST['linkback'] );

}

?>