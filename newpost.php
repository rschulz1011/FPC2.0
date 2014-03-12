<?php

session_start();
require("get_weeknum.php");

if (strlen($_POST['posttext'])>=1)
{
   @ $db = new mysqli('fpcdata.db.8807435.hostedresource.com',
           'fpcdata','bB()*45.ab','fpcdata');
           
    $query = "insert into post set posttext='".$_POST['posttext']."', username ='".
         $_SESSION['username']."', posttime = '".date("c",now_time())."'";
         
    $db->query($query);
         
    header("Location: ".$_POST['linkback'] );

}

?>